<?php

// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Mail\InventoryAlertMail;
// use App\Models\Location;
// use App\Models\LocationCapacityCheck;
// use App\Models\Product;
// use GuzzleHttp\Client;
// use Illuminate\Support\Facades\Mail;

// class InventoryController extends Controller
// {
//     protected $scanCounts = []; // Store scan counts per session

//     public function updateInventory(Request $request)
//     {
//         $client = new Client();

//         // Fetch barcodes from Python
//         $response = $client->get('http://127.0.0.1:5000/get_barcodes');
//         $barcodeData = json_decode($response->getBody()->getContents(), true);
//         $barcodes = $barcodeData['barcodes'] ?? [];

//         if (empty($barcodes)) {
//             return response()->json(["error" => "No barcodes detected"], 404);
//         }

//         $totalScanned = 0;

//         foreach ($barcodes as $barcode) {
//             $product = Product::where('id', $barcode)->first();
//             if ($product) {
//                 $this->scanCounts[$barcode] = ($this->scanCounts[$barcode] ?? 0) + $product->quantity;
//                 $totalScanned += $product->quantity;
//             }
//         }

//         // Fetch location dynamically (modify as needed)
//         // $location = Location::first();
//         // if (!$location) {
//         //     return response()->json(["error" => "Location not found"], 404);
//         // }
//         // $location = Location::where('id', 'L0005')->first();
//         // if (!$location) {
//         //     return response()->json(["error" => "Location not found"], 404);
//         // }
        
//         // $locationId = session('current_rack');
//         // $location = Location::find($locationId);
//         $location = Location::find('L0005');

//         if (!$location) {
//             return response()->json(["error" => "Rack location not found in session"], 404);
//         }

//         // Determine stock status
//         $status = ($totalScanned > $location->capacity) ? 'overstock' : (($totalScanned < ($location->capacity * 0.5)) ? 'understock' : 'normal');

//         // Store the inventory level check result
//         LocationCapacityCheck::create([
//             'location_id' => $location->id,
//             'scan_date' => now(),
//             'detected_capacity' => $totalScanned,
//             'status' => $status
//         ]);

//         // Send an email alert if needed
//         if ($status !== 'normal') {
//             $emailData = [
//                 'location_id' => $location->id,
//                 'detected_capacity' => $totalScanned,
//                 'rack_capacity' => $location->capacity,
//                 'status' => $status
//             ];
        
//             Mail::to('maha1822003@gmail.com')->send(new InventoryAlertMail($emailData));
//         }

//         return response()->json([
//             'status' => $status,
//             'success' => true,
//             'redirect' => route('mainPage',compact('status'))
//         ]);

        
//     }

//     // Reset scan counts
//     public function resetScans()
//     {
//         $this->scanCounts = [];
//         response()->json([
//             'message' => "Scan counts reset",
//             'redirect' => route('mainPage')
//         ]);

//         return redirect()->route('mainPage')->with('success', 'Scan counts reset');

//     }
// }

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\InventoryAlertMail;
use App\Models\Location;
use App\Models\LocationCapacityCheck;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

class InventoryController extends Controller
{
    protected $scanCounts = []; // Store scan counts per session

    public function updateInventory(Request $request)
    {
        $client = new Client();

        // Fetch barcodes from Python
        $response = $client->get('http://127.0.0.1:5000/get_barcodes');
        $barcodeData = json_decode($response->getBody()->getContents(), true);
        $barcodes = $barcodeData['barcodes'] ?? [];

        if (empty($barcodes)) {
            return response()->json(["error" => "No barcodes detected"], 404);
        }

        $totalScanned = 0;
        $productAlerts = [];

        // Count barcode occurrences
        $barcodeCounts = array_count_values($barcodes);

        foreach ($barcodeCounts as $barcode => $count) {
            $product = Product::where('id', $barcode)->first();
            if ($product) {
                $scannedQuantity = $count * $product->quantity; // Multiply by occurrence count
                $this->scanCounts[$barcode] = ($this->scanCounts[$barcode] ?? 0) + $scannedQuantity;
                $totalScanned += $scannedQuantity;
                
                // Check individual product stock levels
                if ($scannedQuantity < $product->min_stock) {
                    $productAlerts[] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'status' => 'understock',
                        'quantity' => $scannedQuantity,
                        'min_stock' => $product->min_stock
                    ];
                } elseif ($scannedQuantity > $product->max_stock) {
                    $productAlerts[] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'status' => 'overstock',
                        'quantity' => $scannedQuantity,
                        'max_stock' => $product->max_stock
                    ];
                }
            }
        }

        $locationId = session('current_rack');
        $location = Location::find($locationId);

        if (!$location) {
            return response()->json(["error" => "Rack location not found in session"], 404);
        }

        // Determine overall rack stock status
        $status = ($totalScanned > $location->capacity) ? 'overstock' : (($totalScanned < ($location->capacity * 0.5)) ? 'understock' : 'normal');

        // Store the inventory level check result
        LocationCapacityCheck::create([
            'location_id' => $location->id,
            'scan_date' => now(),
            'detected_capacity' => $totalScanned,
            'status' => $status
        ]);

        // Send an email alert if needed
        if ($status !== 'normal' || !empty($productAlerts)) {
            $emailData = [
                'location_id' => $location->id,
                'detected_capacity' => $totalScanned,
                'rack_capacity' => $location->capacity,
                'status' => $status,
                'product_alerts' => $productAlerts
            ];
            
            Mail::to('maha1822003@gmail.com')->send(new InventoryAlertMail($emailData));
        }

        return response()->json([
            'status' => $status,
            'product_alerts' => $productAlerts,
            'success' => true,
            'redirect' => route('mainPage', compact('status'))
        ]);
    }

    public function resetScans()
    {
        $this->scanCounts = [];
        return redirect()->route('mainPage')->with('success', 'Scan counts reset');
    }
}
