<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function getAllProducts(){
        try {
            $allProducts = Product::all()->where('status', 1);

            return response()->json([
                'data' => $allProducts->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
