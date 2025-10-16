<?php

namespace App\Http\Controllers\Admin\Inventory;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.admin.inventory.products');
    }

    public function getProductData()
    {
        try {
            $products = Product::with(['productCategoryRS'])->orderBy('name')->get();

            return response()->json([
                'data' => $products->map(function ($product) {
                    return [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'base_price'    => $product->base_price,
                        'category_name' => optional($product->productCategoryRS)->name,
                        'status'        => Status::getStatusText($product->status),
                        'created_at'    => Carbon::parse($product->created_at)->format('M d, Y'),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategories()
    {
        $categories = ProductCategory::whereNot('name', 'All')->orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('img/products', 'public');
            $validatedData['image'] = $path;
        }

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product added successfully!',
            'product' => $product
        ], 201);
    }
}
