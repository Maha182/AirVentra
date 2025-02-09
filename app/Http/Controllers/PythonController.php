<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;

class PythonController extends Controller
{
    public function submit(Request $request)
    {
        // Initialize Guzzle HTTP client
        $client = new Client();

        // Make a GET request to Flask API
        $request = $client->get('http://127.0.0.1:5000/submitData');

        // Get the response from Flask
        $response = json_decode($request->getBody()->getContents(), true);

        // Process the received data
        $id = $response['id'];
        $zone_name = $response['zone_name'];

        // Find product by ID
        $product = Product::where('id', $id)->first();

        if (!$product) {
            return redirect()->route('storage-assignment')->with('error', 'Product not found');
        }

        // Query for location in the specified zone with available capacity
        $location = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->orderBy('aisle')
            ->first();

        if (!$location) {
            return redirect()->route('storage-assignment')->with('error', 'No available storage locations in zone: ' .$zone_name);
        }

        // Assign the location to the product
        $product->location_id = $location->id;
        $product->save();

        // Update capacity of the location
        $location->increment('current_capacity');

        // Store result in session to display on the page
        session()->flash('assigned_product', [
            'product_id' => $product->id,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name
        ]);

        // Redirect back to the dashboard with success message
        return redirect()->route('storage-assignment')->with('message', 'Product Assigned Successfully');
        
    }
}
