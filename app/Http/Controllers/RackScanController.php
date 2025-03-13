<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Location;

class RackScanController extends Controller
{
    public function scanRack()
    {
        $client = new Client();

        // Fetch rack barcode from Python
        $response = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($response->getBody()->getContents(), true);
        $rackBarcode = $barcodeData['barcode'] ?? null;

        if (!$rackBarcode) {
            return response()->json(["error" => "No rack barcode detected"], 404);
        }

        // Find the location using the scanned barcode
        $location = Location::where('id', $rackBarcode)->first();

        if (!$location) {
            return response()->json(["error" => "Rack location not found"], 404);
        }

        // Store the current rack in session
        session()->put('current_rack', $location->id);

        return response()->json([
            'success' => true,
            'rack_id' => $location->id,
            'zone' => $location->zone_name,
            'capacity' => $location->capacity
            
        ]);
    }
}
