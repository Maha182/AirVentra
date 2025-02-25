<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\DataTables\ProductsDataTable;

class ProductController extends Controller
{
    public function index(ProductsDataTable $dataTable)
    {
        $pageTitle = 'Product List';
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('products.create').'" class="btn btn-sm btn-primary">Add New Product</a>';

        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }

    public function create()
    {
        return view('products.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'main_category' => 'required|string',
            'quantity'      => 'required|integer',
            'location_id'   => 'required|integer',
            'barcode_path'  => 'nullable|string',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->withSuccess('Product added successfully!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.form', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'main_category' => 'required|string',
            'quantity'      => 'required|integer',
            'location_id'   => 'required|integer',
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
