<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use App\Models\Location;
use App\Models\Product;
use GuzzleHttp\Client;
use App\Models\LocationCheck;

class StorageAssignmentController extends Controller
{
    public function sendLocationData(Request $request)
    {
        $client = new Client();

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $productID = $barcodeData['barcode'] ?? null;

        // Fetch product from the database
        $product = Product::where('id', $productID)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found.',
            ], 404);
        }

        // Fetch the product description
        $description = $product->description;

        // Send description to the Python API
        $response = $client->post('http://127.0.0.1:5001/getData', [
            'json' => ['description' => $description]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        $zone_name = $result['zone_name'] ?? null;
        // $zone_name = 'Refrigerator Zone';
        if (!$zone_name) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get zone name from Python API.',
            ], 500);
        }

        return $this->assignLocation($product, $zone_name);
    }
    private function assignLocation($product, $zone_name)
    {
        // Query for an available location in the specified zone
        $location = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->orderBy('aisle')
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'error' => 'No available storage locations in zone: ' . $zone_name,
            ], 404);
        }

        // Get all locations in the zone
        $locationsInZone = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->get();

        // Get the freest location (least filled)
        $freestLocation = $locationsInZone->sortBy('current_capacity')->first();

        // Get the nearest location (sorted by aisle and rack)
        $nearestLocation = $locationsInZone->sortBy([['aisle', 'asc'], ['rack', 'asc']])->first();

        // Store product assignment in session
        $assignedProduct = [
            'product_id' => $product->id,
            'product_name' => $product->title,
            'product_description' => $product->description,
            'product_quantity' => $product->quantity,
            'location' => $product->location_id,
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

        $product_id = session('assigned_product')['product_id'] ?? null;
        $location_id = $request->input('selected_location_id'); 

        if (!$product_id || !$location_id) {
            return redirect()->route('storage-assignment')->with('error', 'No assigned product or location selected.');
        }

        $product = Product::find($product_id);
        $location = Location::find($location_id);

        if (!$product || !$location) {
            return redirect()->route('storage-assignment')->with('error', 'Product or location not found.');
        }

        if ($product->location_id == $location->id) {
            return redirect()->route('storage-assignment')->with('info', 'This product is already assigned to this location.');
        }

        if ((int) $location->current_capacity >= (int) $location->capacity) {
            return redirect()->route('storage-assignment')->with('error', 'Location is at full capacity.');
        }

        // Assign location to product
        $product->location_id = $location->id;
        $product->save();
        $location->increment('current_capacity');

        return redirect()->route('storage-assignment')->with('success', 'Product Assigned Successfully');
    }

    // Lookup Location
    public function lookupLocation(Request $request)
    {
        $location = Location::where('id', $request->locationID)->first();

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