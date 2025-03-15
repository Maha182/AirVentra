<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use App\DataTables\ProductsDataTable;

class ProductController extends Controller
{
    public function index(ProductsDataTable $dataTable)
    {
        $pageTitle = 'Product List';
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('products.create').'" class="btn btn-sm btn-primary">Add New Product</a>';

        $zoneCapacity = Location::selectRaw("zone_name, SUM(current_capacity) as used_capacity, SUM(capacity - current_capacity) as free_capacity")
        ->groupBy('zone_name')
        ->get();
    
        return $dataTable->render('global.datatable', compact('pageTitle','zoneCapacity', 'assets', 'headerAction'));
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
