<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use GuzzleHttp\Client;

class StorageAssignmentController extends Controller
{
    public function sendLocationData(Request $request)
    {
        $client = new Client();

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $barcode = $barcodeData['barcode'] ?? null;

        // Fetch product batch by barcode
        $batch = ProductBatch::where('barcode', $barcode)->first();

        if (!$batch || !$batch->product) {
            return response()->json([
                'success' => false,
                'error' => 'Product batch or related product not found.',
            ], 404);
        }

        // Get the product description from the related product
        $description = $batch->product->description;

        // Send description to Python API
        $response = $client->post('http://127.0.0.1:5001/getData', [
            'json' => ['description' => $description]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        $zone_name = $result['zone_name'] ?? null;

        if (!$zone_name) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get zone name from Python API.',
            ], 500);
        }

        return $this->assignLocation($batch, $zone_name);
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
        $location = $preferredLocation ?: $availableLocations->sortBy('aisle')->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'error' => 'No available storage locations in zone: ' . $zone_name,
            ], 404);
        }

        // Step 6: Prepare session data
        $freestLocation = $availableLocations->sortBy('current_capacity')->first();
        $nearestLocation = $availableLocations->sortBy([['aisle', 'asc'], ['rack', 'asc']])->first();

        $assignedProduct = [
            'batch_id' => $batch->id,
            'product_id' => $productId,
            'product_name' => optional($batch->product)->title,
            'product_description' => optional($batch->product)->description,
            'product_quantity' => $batch->quantity,
            'location' => $batch->location_id,
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

        return response()->json([
            'assigned_product' => $assignedProduct,
            'success' => true,
            'locations' => [
                'freest' => $freestLocation ?? null,
                'nearest' => $nearestLocation ?? null,
            ]
        ]);
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
            return redirect()->route('storage-assignment')->with('error', 'Location is at full capacity.');
        }

        // Assign location to batch
        $batch->location_id = $location->id;
        $batch->save();
        $location->increment('current_capacity');

        return redirect()->route('storage-assignment')->with('success', 'Batch Assigned Successfully');
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