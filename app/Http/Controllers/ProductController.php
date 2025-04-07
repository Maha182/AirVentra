<?php

namespace App\Http\Controllers;
use App\Models\ProductBatch;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use App\DataTables\ProductsDataTable;
use Illuminate\Support\Facades\DB; 

class ProductController extends Controller
{
    public function index(ProductsDataTable $dataTable)
    {
        $pageTitle = 'Product List';
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('products.create').'" class="btn btn-sm btn-primary">Add New Product</a>';

        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }

    public function charts()
    {
        // 1. Product Availability (Bubble Chart)
        $products = ProductBatch::select(
                'products.title',
                'product_batches.quantity',
                'locations.aisle',
                'locations.rack',
                'locations.zone_name'
            )
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->leftJoin('locations', 'product_batches.location_id', '=', 'locations.id')
            ->get();
    
        $bubbleData = $products->map(function ($product) {
            return [
                'title' => $product->title,
                'quantity' => $product->quantity,
                'aisle' => $product->aisle,
                'rack' => $product->rack,
                'zone' => $product->zone_name,
            ];
        });
    
        // 2. Zone Capacity: Used vs Free
        $locations = Location::all();
    
        $chartData = [];
        $totalWarehouseCapacity = 0;
        $totalUsedCapacity = 0;
    
        $zones = $locations->groupBy('zone_name');
    
        foreach ($zones as $zoneName => $zoneLocations) {
            $totalUsedCapacityForZone = 0;
    
            foreach ($zoneLocations as $location) {
                $usedCapacity = $location->current_capacity;
                $totalUsedCapacityForZone += $usedCapacity;
    
                $totalWarehouseCapacity += $location->capacity;
                $totalUsedCapacity += $usedCapacity;
            }
    
            $chartData[] = [
                'zone' => $zoneName,
                'used_capacity' => $totalUsedCapacityForZone,
            ];
        }
    
        $totalFreeCapacity = $totalWarehouseCapacity - $totalUsedCapacity;
    
        $chartData[] = [
            'zone' => 'Warehouse Free Capacity',
            'used_capacity' => $totalFreeCapacity,
        ];
    
        // 3. Product Count Per Zone
        $zoneProductCount = DB::table('locations')
            ->select('locations.zone_name', DB::raw('COUNT(DISTINCT product_batches.id) as product_count'))
            ->leftJoin('product_batches', 'locations.id', '=', 'product_batches.location_id')
            ->groupBy('locations.zone_name')
            ->get();
    
        // 4. Optional: Zone-wise used vs free breakdown
        $zoneCapacity = DB::table('locations')
            ->select(
                'zone_name',
                DB::raw('SUM(current_capacity) as used_capacity'),
                DB::raw('SUM(capacity - current_capacity) as free_capacity')
            )
            ->groupBy('zone_name')
            ->get();
    
        return view('product_charts', compact(
            'bubbleData',
            'zoneCapacity',
            'zoneProductCount',
            'chartData'
        ));
    }
    
    
    
    
    
    public function create()
    {
        $locations = Location::pluck('id'); // Fetch both 'id' and 'name' for dropdown
        return view('products.form', [
            "product" => new Product(),
            "locations" => $locations, // Pass locations to the view
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'main_category' => 'required|string',
            'location_id' => 'required|integer',
            'barcode_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image file
        ]);

        // Generate product ID
        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->main_category = $request->main_category;
        $product->quantity = $request->quantity;
        $product->location_id = $request->location_id;
        $product->id = Product::generateProductId($request->main_category); // Call ID generator

        // Handle file upload
        if ($request->hasFile('barcode_image')) {
            $file = $request->file('barcode_image');
            $categoryFolder = strtolower($request->main_category); // Convert category to lowercase
            $fileName = $product->id . '.' . $file->getClientOriginalExtension(); // Rename file with product ID
            $filePath = "images/{$categoryFolder}/{$fileName}";

            // Save file in storage (public/images/{category}/)
            $file->storeAs("{$filePath}");

            // Save path in DB
            $product->barcode_path = $filePath;
        }

        $product->save();

        // return response()->json(['message' => 'Product created successfully', 'product' => $product]);
        return redirect()->route('products.index')->withSuccess(__('Product added successfully!'));
    }

    public function edit($id)
    {
        $locations = Location::pluck('id', 'id'); // ✅ Proper key-value format
        $product = Product::findOrFail($id);
        return view('products.form', compact('product','id','locations'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'main_category' => 'required|string',
            'location_id' => 'required|string|regex:/^L\d{4}$/', // Accepts L0001 format
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->withSuccess('Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->withSuccess('Product deleted successfully!');
    }
}
