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
            'id' => 'required|exists:locations,locationID',
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