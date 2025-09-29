<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->select('id', 'name', 'unit', 'unit_price')
            ->get();
        // dd ($items);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        try {
            $orderItemsJson = $request->input('order_items_payload');
            $items = json_decode($orderItemsJson, true);
            $total_amount = 0;
            foreach ($items as $item) {
                $total_amount += (float)$item['total'];
            }
            // dd($total_amount);
            // dd($items);
            // dd((int)$request->order_id);
            $id = (int)$request->order_id;
            DB::beginTransaction();
            $purchase_req = PurchaseRequest::create([
                'id' => $id,
                'type' => 'Purchase Order Request',
                'department' => 4,
                'amount' => $total_amount,
                'requested_by' => auth('')->user()->id,
                'status' => 11,
            ]);
            $purchase_req->save();
            foreach ($items as $itemData) {
                $purchase_order = PurchaseOrder::create([
                    'type' => 'Purchase Request',
                    'purchase_request_id' => $purchase_req->id,
                    'order_date' => now(),
                    'expected_delivery_date' => null,
                    'delivery_date' => null,
                    'delivery_name' => null,
                    'remarks' => 'pending request',
                    'created_by' => auth('')->user()->id,
                    'supplier_id' => $itemData['supplier_id'],
                    'status' => 11,
                ]);
                $purchase_order->save();
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchase_order->id,
                    'item_id' => $itemData['item_id'],
                    'category_id' => $itemData['category_id'],
                    'quantity' => $itemData['qnty'],
                    'unit_price' => $itemData['unit_price'],
                    'total_amount' => $itemData['total'],
                    'backorder_qnty' => null,
                    'delivered_qnty' => null,
                    'status' => 11,
                ]);
            }
            
            DB::commit();

            return response()->json(['message' => 'Purchase Request submitted successfully!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Controller Error: ' . $e->getMessage()], 500);
        }
    }
}
