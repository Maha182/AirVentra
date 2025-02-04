<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use App\Models\Location;
use App\Models\Product;

class StorageAssignmentController extends Controller
{
    // Lookup Location
    public function lookupLocation(Request $request)
    {
        $location = Location::where('locationID', $request->locationID)->first();

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
            'locationID' => 'required|exists:locations,locationID',
            'zone_name' => 'required',
            'aisle' => 'required',
            'rack' => 'required',
        ]);

        // Update the existing location with new data
        $location = Location::where('locationID', $request->locationID)->first();
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

    public function storage(Request $request)
        {
        // Force Laravel to read JSON requests
        $data = $request->json()->all();

        Log::info('Storage function called', $data); // Log incoming request

        // Validate input
        $validated = Validator::make($data, [
            'productID' => 'required|exists:products,productID',
            'zone_name' => 'required|string'
        ]);

        if ($validated->fails()) {
            Log::error('Validation failed', ['errors' => $validated->errors()]);
            return response()->json(['error' => $validated->errors()], 400);
        }

        Log::info('Validation passed');

        // Find an unfilled location within the specified zone
        $location = Location::where('zone_name', $data['zone_name'])
            ->whereColumn('current_capacity', '<', 'capacity')
            ->first();

        if (!$location) {
            Log::error('No available locations found in zone: ' . $data['zone_name']);
            return response()->json(['error' => 'No available locations found in this zone'], 404);
        }

        Log::info('Location found', ['locationID' => $location->locationID]);

        // Assign the location to the product
        $product = Product::where('productID', $data['productID'])->first();
        if (!$product) {
            Log::error('Product not found with ID: ' . $data['productID']);
            return response()->json(['error' => 'Product not found.'], 404);
        }

        Log::info('Product found', ['productID' => $product->productID]);

        // Update product location
        $product->update(['location_id' => $location->locationID]);
        Log::info('Product assigned to location', ['productID' => $product->productID, 'locationID' => $location->locationID]);

        // Update the location's current capacity
        $location->update(['current_capacity' => $location->current_capacity + 1]);
        Log::info('Location capacity updated', ['locationID' => $location->locationID, 'current_capacity' => $location->current_capacity]);

        return response()->json([
            'success' => 'Product assigned to a location successfully',
            'assigned_location' => [
                'locationID' => $location->locationID,
                'zone_name' => $location->zone_name,
                'aisle' => $location->aisle,
                'rack' => $location->rack
            ]
        ], 200);
    }


    
}