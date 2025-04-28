<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\InventoryAlertMail;
use App\Models\Location;
use App\Models\LocationCapacityCheck;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use App\Models\ProductBatch;

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

        $locationData = session('current_rack');  // Get the full session data
        $locationId = $locationData['rack_id'];   // Extract just the rack_id

        $location = Location::find($locationId);

        if (!$location) {
            return response()->json(["error" => "Rack location not found in session"], 404);
        }

        // Shelf overfill/underfill check
        $status = ($totalScanned > $location->capacity)
            ? 'overfilled'
            : (($totalScanned < ($location->capacity * 0.5)) ? 'underfilled' : 'normal');


        $location->current_capacity = $totalScanned;

        $capacityCheck = LocationCapacityCheck::create([
            'location_id' => $location->id,
            'scan_date' => now(),
            'detected_capacity' => $totalScanned,
            'status' => $status
        ]);

        // Email alert if over/underfilled
        $shouldAlert = $status !== 'normal' || !empty($expiredBatches) || !empty($soonToExpireBatches);

        if ($shouldAlert) {
            $taskController = new TaskAssignmentController();
            $assignedEmployee = $taskController->assignTask($capacityCheck->id, 'capacity');

            Mail::to($assignedEmployee->email)->send(new InventoryAlertMail([
                'location_id' => $location->id,
                'detected_capacity' => $totalScanned,
                'rack_capacity' => $location->capacity,
                'status' => $status,
                'expired_batches' => $expiredBatches,
                'soon_to_expire_batches' => $soonToExpireBatches
            ]));
        }

        return response()->json([
            'status' => $status,
        ]);
     
    }

    // Reset scan counts
    // Reset scan counts and clear rack session
    public function resetScans()
    {
        $this->scanCounts = [];

        return redirect()->route('ScanShelf')->with('success', 'Scan counts and rack session reset');
    }

}
