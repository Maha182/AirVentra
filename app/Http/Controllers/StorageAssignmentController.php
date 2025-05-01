<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class StorageAssignmentController extends Controller
{
    public function sendLocationData(Request $request)
    {
        $client = new Client();

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $barcode = trim($barcodeData['barcode'] ?? '');
    
        if (!$barcode) {
            return response()->json(['success' => false, 'error' => 'No barcode detected.'], 400);
        }
    
        // Try to find batch
        $batch = ProductBatch::where('barcode', $barcode)->first();
        $batchWasNew = false;
    
        // If batch doesn't exist, parse and add it
        if (!$batch) {
            \Log::info("⚠️ Batch is new: $barcode");
            // Parse barcode
            preg_match('/^([A-Z0-9]+)-(\d{6})-(\d{1,3})-([A-Z0-9]+)$/', $barcode, $matches);

            if (!$matches || count($matches) !== 5) {
                \Log::warning("❌ Barcode did not match expected format: $barcode");
                return response()->json([
                    'success' => false,
                    'message2' => 'Invalid barcode format.'
                ], 400);
            }
        
            [$_, $productId, $expiryStr, $quantity, $batchCode] = $matches;

            // Extract the components manually
            $year = substr($expiryStr, 0, 2);   // '25'
            $month = substr($expiryStr, 2, 2);  // '04'
            $day = substr($expiryStr, 4, 2);    // '12'
            

            // Force 20xx interpretation
            $fullYear = 2000 + (int)$year;

            // Now build expiry date
            $expiryDateFormatted = Carbon::createFromDate($fullYear, $month, $day)->format('Y-m-d');

            
        
            $product = Product::find($productId);
        
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product ID from barcode does not exist: ' . $productId
                ], 404);
            }
        
            // Create new batch
            $batch = ProductBatch::create([
                'product_id' => $productId,
                'barcode' => trim($barcode),
                'quantity' => (int)$quantity,
                'expiry_date' => $expiryDateFormatted, 
                'received_date' => now(),
                'status' => 'in_stock',
            ]);
            
        
            $batchWasNew = true;
        }
    
        // Continue with location assignment
        $description = $batch->product->description;
    
        $response = $client->post('http://127.0.0.1:5001/getData', [
            'json' => ['description' => $description]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        $zone_name = $result['zone_name'] ?? null;

        if (!$zone_name) {
            return response()->json(['success' => false, 'error' => 'Zone assignment failed.'], 500);
        }
        $data = $this->assignLocation($batch, $zone_name);

        // Add extra message if batch was new
        if ($batchWasNew) {
            $data['message'] = '✅ The product was not in the stock of the warehouse and has been added.';
        }
        
        return response()->json($data);
        

        
    }

    private function assignLocation($batch, $zone_name)
    {
        $productId = $batch->product_id;


          // Query for an available location in the specified zone
        // $location = Location::where('zone_name', $zone_name)
        //     ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
        //     ->orderBy('aisle')
        //  

        // Step 1: Get location_ids where this product already exists
        $existingBatchLocations = ProductBatch::where('product_id', $productId)
            ->whereNotNull('location_id')
            ->pluck('location_id')
            ->unique()
            ->toArray();

        // Step 2: Load full location info from those IDs
        $existingLocations = Location::whereIn('id', $existingBatchLocations)
            ->where('zone_name', $zone_name)
            ->get();

        // Step 3: Get all available locations in the zone
        $availableLocations = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->get();

        $preferredLocation = null;

        // Step 4: Try to find a location near the existing ones
        if (!$existingLocations->isEmpty()) {
            $preferredLocation = $availableLocations->sortBy(function ($location) use ($existingLocations) {
                return $existingLocations->min(function ($existing) use ($location) {
                    // If aisle or rack is null or not numeric, set default distance high
                    $aisleDiff = is_numeric($existing->aisle) && is_numeric($location->aisle)
                        ? abs((int)$existing->aisle - (int)$location->aisle)
                        : 999;

                    $rackDiff = is_numeric($existing->rack) && is_numeric($location->rack)
                        ? abs((int)$existing->rack - (int)$location->rack)
                        : 999;

                    return $aisleDiff + $rackDiff;
                });
            })->first();
        }

        // Step 5: Fallback to a default available location if no preferred
        $location = $preferredLocation ?: $availableLocations->sortBy('current_capacity')->first();
        if (!$location) {
            return [
                'success' => false,
                'message2' => "❌ The zone '{$zone_name}' is currently full. Please assign manually.",
            ];
        }
        
        

        // Step 6: Prepare session data
        $freestLocation = $availableLocations->sortBy('current_capacity')->first();
        $nearestLocation = $availableLocations->sortBy([['aisle', 'asc'], ['rack', 'asc']])->first();

        $assignedProduct = [
            'batch_id' => $batch->id,
            'product_id' => $productId,
            'product_name' => $batch->product->title,
            'batch_quantity' => $batch->quantity,
            'assigned_location' => $location,
            'zone_name' => $location->zone_name,
            'aisle' => $location->aisle,
            'rack' => $location->rack,
            'current_capacity' => $location->current_capacity,
            'capacity' => $location->capacity,
            'freest' => $freestLocation ?? null,
            'nearest' => $nearestLocation ?? null,
        ];

        session()->put('assigned_product', $assignedProduct);

        return [
            'assigned_product' => $assignedProduct,
            'success' => true,
        ];
        
    }

    public function assignProductToLocation(Request $request)
    {
        $batch_id = session('assigned_product')['batch_id'] ?? null;
        $location_id = $request->input('selected_location_id');

        if (!$batch_id || !$location_id) {
            return redirect()->route('storage-assignment')->with('error', 'No assigned batch or location selected.');
        }

        $batch = ProductBatch::find($batch_id);
        $location = Location::find($location_id);

        if (!$batch || !$location) {
            return redirect()->route('storage-assignment')->with('error', 'Batch or location not found.');
        }

        if ($batch->location_id == $location->id) {
            return redirect()->route('storage-assignment')->with('info', 'This batch is already assigned to this location.');
        }

        if ((int)$location->current_capacity >= (int)$location->capacity) {
            if ($request->has('from_modal')) {
                return redirect()->route('storage-assignment')
                    ->with('modal_error', 'Location is at full capacity.')
                    ->with('open_modal', true);
            } else {
                return redirect()->route('storage-assignment')
                    ->with('error', 'Location is at full capacity.');
            }
        }

        $batch->location_id = $location->id;
        $batch->save();
        $location->current_capacity = $location->current_capacity + $batch->quantity;

        // ✅ Detect if manual modal is used based on presence of "open_modal"
        if ($request->has('from_modal')) {
            return redirect()->route('storage-assignment')
                ->with('modal_success', '✅ Location assigned manually!')
                ->with('open_modal', true);
        }

        return redirect()->route('storage-assignment')
            ->with('success', '✅ Batch Assigned Successfully');
    }

    public function lookupLocation(Request $request)
    {
        $location = Location::find($request->locationID);

        if ($location) {
            return response()->json([
                'success' => true,
                'data' => [
                    'zone_name' => $location->zone_name,
                    'aisle' => $location->aisle,
                    'rack' => $location->rack,
                ]
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    // Assign Location
    public function assignManual(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:locations,id',
            'zone_name' => 'required',
            'aisle' => 'required',
            'rack' => 'required',
        ]);

        // Update the existing location with new data
        $location = Location::where('id', $request->id)->first();
        if ($location) {
            $location->update([
                'zone_name' => $request->zone_name,
                'aisle' => $request->aisle,
                'rack' => $request->rack,
            ]);

            return redirect()->back()->with('success', 'Location Updated Successfully!');
        }

        return redirect()->back()->with('error', 'These credentials do not match our records.');
    }
}