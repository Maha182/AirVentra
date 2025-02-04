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

    public function assignStorage()
    {
        // Fixed product ID for now
        $productID = '1'; // Change this as needed
        $zoneName = 'Refrigerator Zone';
    
        // Find the product by its ID
        $product = Product::find($productID);
        if (!$product) {
            return redirect()->back()->with('error', 'Product with ID ' . $productID . ' not found');
        }
        // Query to find available location within the specified zone
        $locationQuery = Location::where('zone_name', $zoneName)
        ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
        ->orderBy('aisle');
    
                                

        \Log::info($locationQuery->toSql(), $locationQuery->getBindings()); // Debug query

        $location = $locationQuery->first();


        if (!$location) {
            return redirect()->back()->with('error', 'No available storage locations in zone: ' . $zoneName);
        }
    
        // Assign the location to the product
        $product->location_id = $location->locationID;
        $product->save();
    
        // Update the current capacity of the location
        $location->increment('current_capacity');
    
        // Store info in session
        return redirect()->back()->with('storage_info', [
            'product_id' => $productID,
            'assigned_location' => $location->locationID,
            'zone_name' => $location->zone_name
        ]);

        return redirect()->back()->with('success', 'product added to a specific location');
    }
    

}