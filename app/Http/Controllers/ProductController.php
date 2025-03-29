<?php

namespace App\Http\Controllers;

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
        $products = Product::select('products.title', 'products.quantity', 'locations.aisle', 'locations.rack', 'locations.zone_name')
            ->join('locations', 'products.location_id', '=', 'locations.id')
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
    
        // 2. Warehouse Capacity Utilization Per Zone
        $zoneCapacity = Location::selectRaw("zone_name, SUM(current_capacity) as used_capacity, SUM(capacity - current_capacity) as free_capacity")
            ->groupBy('zone_name')
            ->get();
    
       
            $zoneProductCount = Location::select(
                'zone_name', 
                DB::raw('COALESCE(COUNT(products.id), 0) as product_count') // Ensures NULL is treated as 0
            )
            ->leftJoin('products', 'locations.id', '=', 'products.location_id')
            ->groupBy('zone_name')
            ->get();

    
            $zoneCapacity = Location::selectRaw("zone_name, SUM(current_capacity) as used_capacity, SUM(capacity - current_capacity) as free_capacity")
        ->groupBy('zone_name')
        ->get();

       
                // Get all locations
        $locations = Location::all();

        $chartData = [];
        $totalWarehouseCapacity = 0;
        $totalUsedCapacity = 0;

        // Group locations by their zone name
        $zones = $locations->groupBy('zone_name');

        // Prepare data for each zone (only used capacity)
        foreach ($zones as $zoneName => $zoneLocations) {
            $totalUsedCapacityForZone = 0;

            // Sum used capacities for each zone
            foreach ($zoneLocations as $location) {
                $usedCapacity = $location->current_capacity;
                $totalUsedCapacityForZone += $usedCapacity;

                // Sum the total warehouse capacity and used capacity
                $totalWarehouseCapacity += $location->capacity;
                $totalUsedCapacity += $usedCapacity;
            }

            // Prepare the chart data for each zone
            $chartData[] = [
                'zone' => $zoneName,
                'used_capacity' => $totalUsedCapacityForZone,
            ];
        }

        // Calculate the free capacity for the whole warehouse
        $totalFreeCapacity = $totalWarehouseCapacity - $totalUsedCapacity;

        // Add the free capacity for the whole warehouse to the chart data
        $chartData[] = [
            'zone' => 'Warehouse Free Capacity',
            'used_capacity' => $totalFreeCapacity,  // This represents the free capacity
        ];

        

        
        

       
    
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
            'quantity' => 'required|integer',
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
        $locations = Location::pluck('id');
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
            'quantity'      => 'required|integer',
            'location_id' => 'required|string|regex:/^L\d{4}$/', // Accepts L0001 format
            'barcode_path'  => 'nullable|string',
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
