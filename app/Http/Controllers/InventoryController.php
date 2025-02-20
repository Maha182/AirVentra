<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\InventoryAlertMail;
use App\Models\Location;
use App\Models\LocationCapacityCheck;
use App\Models\Product;

class InventoryController extends Controller
{
    protected $scanCounts = []; // Store scan counts per session

    public function updateInventory(Request $request)
    {

        $client = new Client();
        $redirectTo = request()->input('redirect_to', 'storage-assignment'); 

        // Fetch barcode from Python
        $barcode = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcode->getBody()->getContents(), true);
        $productID = $barcodeData['barcode'] ?? null;


        // In your database, the barcode is actually the product ID
        $product = Product::find($barcode);

        if (!$product) {
            return response()->json(["error" => "Product not found"], 404);
        }

        // Get the location of the product
        // $location = Location::where('id', $product->location_id)->first();
        // if (!$location) {
        //     return response()->json(["error" => "Location not found"], 404);
        // }
        $Location = 'L0005';
        // Keep track of the number of times this product has been scanned
        if (!isset($this->scanCounts[$barcode])) {
            $this->scanCounts[$barcode] = 0;
        }
        
        // Add the product's quantity each time it's scanned
        $this->scanCounts[$barcode] += $product->quantity;

        // Sum total scanned products in this location
        $totalScanned = array_sum($this->scanCounts);

        // Determine stock status
        $status = ($totalScanned > $location->capacity) ? 'overstock' : (($totalScanned < ($location->capacity * 0.5)) ? 'understock' : 'normal');

        // Store the scan result
        LocationCapacityCheck::create([
            'location_id' => $location->id,
            'scan_date' => now(),
            'detected_capacity' => $totalScanned,
            'status' => $status
        ]);

        // Send an email alert if needed
        if ($status !== 'normal') {
            $emailData = [
                'location_id' => $location->id,
                'detected_capacity' => $totalScanned,
                'rack_capacity' => $location->capacity,
                'status' => $status
            ];
        
            Mail::to('maha1822003@gmail.com')->send(new InventoryAlertMail($emailData));
        }

        return response()->json([
            "message" => "Inventory updated",
            "status" => $status,
            "total_scanned" => $totalScanned
        ]);
    }

    // Reset the scan count for a new rack
    public function resetScans()
    {
        $this->scanCounts = [];
        return response()->json(["message" => "Scan counts reset"]);
    }
}