<?php

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
        $expiredBatches = [];
        $soonToExpireBatches = [];
        $unknownBatches = [];

        foreach ($barcodes as $barcode) {
            $batch = ProductBatch::where('barcode', $barcode)->first();

            if ($batch) {
                // Sum quantities for shelf check
                $totalScanned += $batch->quantity;

                // Check expiry
                if ($batch->expiry_date) {
                    $today = Carbon::today();
                    $expiry = Carbon::parse($batch->expiry_date);

                    if ($expiry->lt($today)) {
                        $batch->status = 'expired';
                        $expiredBatches[] = $batch;
                    } elseif ($expiry->diffInDays($today) <= 5) {
                        $batch->status = 'soon_to_expire';
                        $soonToExpireBatches[] = $batch;
                    }

                    $batch->save();
                }

                $this->scanCounts[$batch->product_id] = ($this->scanCounts[$batch->product_id] ?? 0) + $batch->quantity;

            } else {
                $unknownBatches[] = $barcode; // Not found in DB
            }
        }

        $locationId = session('current_rack');
        $location = Location::find($locationId);

        if (!$location) {
            return response()->json(["error" => "Rack location not found in session"], 404);
        }

        // Shelf overfill/underfill check
        $status = ($totalScanned > $location->capacity)
            ? 'overfilled'
            : (($totalScanned < ($location->capacity * 0.5)) ? 'underfilled' : 'normal');

        $capacityCheck = LocationCapacityCheck::create([
            'location_id' => $location->id,
            'scan_date' => now(),
            'detected_capacity' => $totalScanned,
            'status' => $status
        ]);

        // Email alert if over/underfilled
        if ($status !== 'normal') {
            $taskController = new TaskAssignmentController();
            $assignedEmployee = $taskController->assignTask($capacityCheck->id);

            Mail::to($assignedEmployee->email)->send(new InventoryAlertMail([
                'location_id' => $location->id,
                'detected_capacity' => $totalScanned,
                'rack_capacity' => $location->capacity,
                'status' => $status,
                'expired' => $expiredBatches,
                'soon' => $soonToExpireBatches
            ]));
        }

        // Email alert if there are expired or soon-to-expire batches
        // if (!empty($expiredBatches) || !empty($soonToExpireBatches)) {
        //     Mail::to($assignedEmployee)->send(new ExpiryAlertMail([
        //         'expired' => $expiredBatches,
        //         'soon' => $soonToExpireBatches
        //     ]));
        // }

        return response()->json([
            'status' => $status,
            'expired_batches' => $expiredBatches,
            'soon_to_expire' => $soonToExpireBatches,
            'unknown_barcodes' => $unknownBatches,
            'success' => true,
            'redirect' => route('mainPage', compact('status'))
        ]);
    }

    // Reset scan counts
    public function resetScans()
    {
        $this->scanCounts = [];
        return redirect()->route('mainPage')->with('success', 'Scan counts reset');
    }
}
