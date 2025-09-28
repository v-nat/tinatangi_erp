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

    // public function store(Request $request)
    // {
    //     // The request->input('order_items') will be a JSON string.
    //     $orderItemsJson = $request->input('order_items');

    //     // Convert the JSON string into a PHP array or collection
    //     $items = json_decode($orderItemsJson, true);

    //     // $items is now a standard PHP array of arrays, e.g.:
    //     // [
    //     //     ['item' => 'Pen', 'qnty' => 10, 'unit' => 5.00, 'total' => 50.00],
    //     //     ['item' => 'Notebook', 'qnty' => 5, 'unit' => 20.00, 'total' => 100.00],
    //     // ]

    //     // You can now loop through $items and save each item to your database.

    //     // Example:
    //     foreach ($items as $itemData) {
    //         // Save $itemData to your order_details table
    //         // OrderDetail::create([...$itemData, 'order_id' => $newOrder->id]);
    //     }

    //     // ... rest of your logic
    // }
}
