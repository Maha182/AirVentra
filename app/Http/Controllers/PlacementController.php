<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\LocationCheck;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlacementErrorMail;
use Carbon\Carbon;


class PlacementController extends Controller
{
    public function checkPlacement(Request $request)
    {
        $client = new Client();
        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $productId = $barcodeData['barcode'] ?? null;


        $scannedLocation = 'L0006';

        // Fetch product details
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $correctLocation = $product->location_id;

        if ($scannedLocation === $correctLocation) {
            return redirect()->back()->with('success', 'The product is in the correct place.');
        } else {
            // Log the error in placement_error table
            LocationCheck::create([
                'product_id' => $productId,
                'scan_date' => Carbon::now(),
                'wrong_location' => $scannedLocation,
                'correct_location' => $correctLocation,
                'status' => 'Pending'
            ]);

            // Send an email notification
            $emailData = [
                'product' => $product,
                'wrong_location' => $scannedLocation,
                'correct_location' => $correctLocation
            ];

            Mail::to('maha1822003@gmail.com')->send(new PlacementErrorMail($emailData));

            return redirect()->back()->with('error', 'The product is in the wrong place. Notification sent.');
        }
    }
}
