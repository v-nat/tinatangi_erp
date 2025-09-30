<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\BudgetRelease;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            $id = (int)$request->order_id;
            DB::beginTransaction();
            $purchase_req = PurchaseRequest::create([
                'id' => $id,
                'type' => 'Purchase Order Request',
                'department' => 4,
                'amount' => $total_amount,
                'requested_by_id' => auth('')->user()->id,
                'requested_date' => now(),
                'remarks' => 'to approve from finance',
                'status' => 11,
            ]);
            $purchase_req->save();
            foreach ($items as $itemData) {
                $purchase_order = PurchaseOrder::create([
                    'type' => 'Purchase Request',
                    'purchase_request_id' => $purchase_req->id,
                    'order_date' => null,
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

    public function putOnProcess(Request $request, $id, $status)
    {
        try {
            DB::beginTransaction();

            $pr = PurchaseRequest::findOrFail($id);
            $pr->remarks = $request->remarks;
            $pr->status = $status;
            $pr->save();

            $prpo = PurchaseOrder::where('purchase_order_id', $id)->first();
            $prpo->remarks = $request->remarks;
            $prpo->status = $status;
            $prpo->save();

            $prpod = PurchaseOrderDetail::where('purchase_order_id', $id)->first();
            $prpod->status = $status;
            $prpod->save();

            if ($status == 14) {
                $year = Carbon::now()->format('Y');
                do {
                    $random = rand(10000, 99999);
                    $release_id = $year . $random;
                } while (BudgetRelease::pluck('id')->contains($release_id));

                $release = BudgetRelease::create([
                    'release_id'        => $release_id,
                    'type'              => 'Purchase Order',
                    'amount'            => $pr->amount,
                    'request_id'        => $id,
                    'requested_by_id'   => auth('')->user()->id,
                    'requested_at'      => now(),
                    'released_by_id'    => null,
                    'released_at'       => null,
                    'department'        => 4,
                    'notes'             => '',
                    'status'            => 11,
                ]);

                $release->save();
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Purchase Request is now on Process!'], 200);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Purchase Request Rejected!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
