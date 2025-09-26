<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    //

    public function getCategories()
    {
        try {
            $categories = Category::all('id', 'name');

        return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getItems(Request $request)
    {
        $category = $request->input('category');
        // dd  ($category);
        if (!$category) {
            return response()->json(['error' => 'Missing Category'], 400);
        }
        $items = Item::incategory($category)->get();
        // $items = $items->map(function ($position) {
        //     return ['id'=> $position->id,'name'=> $position->name];
        // });
        $items = Item::where('category_id', $category)
            ->select('id', 'name')
            ->get();
        // dd ($items);

        return response()->json($items);
    }
}
