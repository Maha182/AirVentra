<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;

class PythonController extends Controller
{
    public function sendLocationData(Request $request)
    {
        $client = new Client();
        $redirectTo = request()->input('redirect_to', 'storage-assignment'); 

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $productID = $barcodeData['barcode'] ?? null;

        if (!$productID) {
            return redirect()->route($redirectTo)->with('error', 'No barcode detected');

        }

        // Fetch product from the database
        $product = Product::where('id', $productID)->first();

        if (!$product) {
            return redirect()->route($redirectTo)->with('error', 'Product not found');
        }

        $description = $product->description;

        // Send description to the Python API
        $response = $client->post('http://127.0.0.1:5001/getData', [
            'json' => ['description' => $description]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        $zone_name = $result['zone_name'] ?? null;
        return $this->assignLocation($product, $zone_name, $redirectTo);
    }

    private function assignLocation($product, $zone_name, $redirectTo)
    {
        if (!$product) {
            return redirect()->route($redirectTo)->with('error', 'Product not found.');

        }

        // Query for an available location in the specified zone
        $location = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->orderBy('aisle')
            ->first();

        if (!$location) {
            session()->forget('assigned_product');
            return redirect()->route($redirectTo)->with('error', 'No available storage locations in zone: ' . $zone_name);
        }

        // Store product assignment in session
        session()->flash('assigned_product', [
            'product_id' => $product->id,
            'product_name' => $product->title,
            'product_description' => $product->description,
            'product_quantity' => $product->quantity,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name,
            'aisle' => $location->aisle,
            'rack' => $location->rack
        ]);

        return redirect()->route($redirectTo);
    }


    public function assignProductToLocation(Request $request)
    {
        // Retrieve product_id and location_id from session
        $product_id = session('assigned_product.product_id');
        $location_id = session('assigned_product.assigned_location');

        // Ensure product_id and location_id exist in session
        if (!$product_id || !$location_id) {
            return redirect()->route('storage-assignment')->with('error', 'No assigned product or location found in session.');
        }

        $product = Product::find($product_id);
        $location = Location::find($location_id);

        // Ensure valid product and location instances
        if (!$product) {
            return redirect()->route('storage-assignment')->with('error', 'Product not found.');
        }

        if (!$location) {
            return redirect()->route('storage-assignment')->with('error', 'Location not found.');
        }

        if ($product->location_id == $location->id) {
            return redirect()->route('storage-assignment')->with('info', 'This product is already assigned to this location.');
        }

        // Prevent assigning if current_capacity is already at maximum
        if ((int) $location->current_capacity >= (int) $location->capacity) {
            return redirect()->route('storage-assignment')->with('error', 'Location is at full capacity.');
        }

        // Assign location to product
        $product->location_id = $location->id;
        $product->save();

        $location->increment('current_capacity');

        session()->flash('assigned_product', [
            'product_id' => $product->id,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name,
            'aisle' => $location->aisle,
            'rack' => $location->rack
        ]);

        return redirect()->route('storage-assignment')->with('success', 'Product Assigned Successfully');
    }



}

