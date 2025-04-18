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



        // Get all products with their batches
        $products = Product::with(['batches' => function($query) {
            $query->where('status', 'in_stock');
        }])->get();
        
        // Calculate stock status for each product
        $products->each(function($product) {
            $totalStock = $product->batches->sum('quantity');
            $product->total_stock = $totalStock;
            
            // Determine stock status
            if ($product->max_stock !== null && $totalStock > $product->max_stock) {
                $product->stock_status = 'overstock';
                $product->status_class = 'bg-danger'; // Red for overstock
            } elseif ($totalStock < $product->min_stock) {
                $product->stock_status = 'understock';
                $product->status_class = 'bg-warning'; // Yellow for understock
            } else {
                $product->stock_status = 'normal';
                $product->status_class = 'bg-success'; // Green for normal
            }
        });
    
        return view('product_charts', compact(
            'products',
            'bubbleData',
            'zoneCapacity',
            'zoneProductCount',
            'chartData'
        ));
    }
    


    // public function stockLevels()
    // {
    //     // Get all products with their batches
    //     $products = Product::with(['batches' => function($query) {
    //         $query->where('status', 'in_stock');
    //     }])->get();
        
    //     // Calculate stock status for each product
    //     $products->each(function($product) {
    //         $totalStock = $product->batches->sum('quantity');
    //         $product->total_stock = $totalStock;
            
    //         // Determine stock status
    //         if ($product->max_stock !== null && $totalStock > $product->max_stock) {
    //             $product->stock_status = 'overstock';
    //             $product->status_class = 'bg-danger'; // Red for overstock
    //         } elseif ($totalStock < $product->min_stock) {
    //             $product->stock_status = 'understock';
    //             $product->status_class = 'bg-warning'; // Yellow for understock
    //         } else {
    //             $product->stock_status = 'normal';
    //             $product->status_class = 'bg-success'; // Green for normal
    //         }
    //     });
        
    //     return view('product_charts', compact('products'));
    // }

    
    
    public function create()
    {
        return view('products.form', [
            "product" => new Product(),
        ]);
    }
    


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'main_category' => 'required|string',
            'min_stock'     => 'required|integer|min:0',
            'max_stock'     => 'nullable|integer|gt:min_stock', // Optional but must be greater than min_stock
        ]);
    
        $product = new Product();
        $product->id            = Product::generateProductId($request->main_category);
        $product->title         = $validated['title'];
        $product->description   = $validated['description'];
        $product->main_category = $validated['main_category'];
        $product->min_stock     = $validated['min_stock'];
        $product->max_stock     = $validated['max_stock'];
    
        $product->save();
    
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
            'min_stock'     => 'required|integer|min:0',
            'max_stock'     => 'required|integer|min:1|gte:min_stock',
            'stock_status'  => 'nullable|string',
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
