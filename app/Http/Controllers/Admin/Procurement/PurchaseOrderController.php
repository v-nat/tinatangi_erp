<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\BudgetRelease;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateIdController;
use App\Models\DeliveryReturn; // Added this
use Illuminate\Support\Facades\Log; // Added for debugging
use Illuminate\Support\Facades\Validator; // Added for validation
use Illuminate\Support\Facades\Storage; // Added for file handling

class PurchaseOrderController extends Controller
{
    public function getCategories()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
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
        $items = Item::with(['unitRS'])->where('category_id', $category)
            ->get();

        return response()->json(
            $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit_id' => optional($item->unitRS)->abbreviation,
                    'unit_price' => (float)$item->unit_price,
                ];
            })
        );
    }

    public function store(Request $request)
    {
        try {
            $orderItemsJson = $request->input('order_items_payload');
            $items = json_decode($orderItemsJson, true);
            $total_amount = 0;
            $supplier_id = null;
            foreach ($items as $item) {
                $total_amount += (float)$item['total'];
                $supplier_id = $item['supplier_id'];
            }
            $id = $request->order_id;
            DB::beginTransaction();
            $purchase_req = PurchaseRequest::create([
                'id' => (int)$id,
                'type' => 'Purchase Order Request',
                'department' => 4,
                'amount' => $total_amount,
                'requested_by_id' => auth('')->user()->id,
                'requested_date' => now(),
                'remarks' => '',
                'supplier_id' => $supplier_id,
                'status' => 11,
            ]);
            $purchase_req->save();
            foreach ($items as $itemData) {
                $purchase_order = PurchaseOrder::create([
                    'type' => 'Purchase Request',
                    'purchase_orderId' => (int)$id,
                    'purchase_request_id' => $purchase_req->id,
                    'order_date' => null,
                    'expected_delivery_date' => null,
                    'delivery_date' => null,
                    'delivery_name' => null,
                    'remarks' => 'pending request',
                    'created_by_id' => auth('')->user()->id,
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
                    'sku' => null,
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
    public function sendReq(Request $request)
    {
        try {
            $id = $request->order_id;
            $supplier = $request->supplier;
            DB::beginTransaction();
            $purchase_req = PurchaseRequest::where('id', $id)->first();
            $purchase_req->type = 'Purchase Order Request';
            $purchase_req->supplier_id = $supplier;
            $purchase_req->status = 11;
            $purchase_req->save();
            $purchase_order = PurchaseOrder::where('purchase_request_id', $id)->first();
            $purchase_order->supplier_id = $supplier;
            $purchase_order->remarks = 'pending request';
            $purchase_order->status = 11;
            $purchase_order->save();
            $purchase_order_detail = PurchaseOrderDetail::where('purchase_order_id', $purchase_order->id)->first();
            $purchase_order_detail->status = 11;
            $purchase_order_detail->save();
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

            if ($request->remarks) {
                $pr->remarks = $request->remarks;
                $pr->status = $status;
                $pr->save();

                $orderInstances = PurchaseOrder::where('purchase_request_id', $id)->pluck('id');
                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->remarks = $request->remarks;
                    $prpo->status = $status;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('purchase_order_id', $orderInstance)->get();
                    foreach($prpod as $detail){
                        $detail->status = $status;
                        $detail->save();
                    }
                }
            } else {
                $pr->remarks = 'requesting budget';
                $pr->status = $status;
                $pr->save();

                $orderInstances = PurchaseOrder::where('purchase_request_id', $id)->pluck('id');

                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->remarks = 'requesting budget';
                    $prpo->status = $status;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('purchase_order_id', $orderInstance)->get();
                    foreach($prpod as $detail){
                        $detail->status = $status;
                        $detail->save();
                    }
                }
            }

            if ($status == 14) {
                $release_id = GenerateIdController::generateID('release_id');

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
                    'notes'             => 'requesting budget',
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

    public function processPurchaseOrders(Request $request, $id, $status)
    {
        try {
            DB::beginTransaction();

            $pr = PurchaseRequest::findOrFail($id);

            if ($status == 21) {
                $pr->remarks = 'requesting approval';
                $pr->status = $status;
                $pr->save();

                $orderInstances = PurchaseOrder::where('purchase_request_id', $id)->pluck('id');

                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->order_date = now();
                    $prpo->remarks = 'requesting approval';
                    $prpo->status = $status;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('purchase_order_id', $orderInstance)->get();
                    foreach($prpod as $detail){
                        $detail->status = $status;
                        $detail->save();
                    }
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Purchase Order Sent!'], 200);
            } else if ($status == 20) {
                $orderInstances = PurchaseOrder::where('purchase_request_id', $id)->pluck('id');
                $supplier_id = null;
                $firstIteration = true;
                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->remarks = 'order accepted';
                    $prpo->status = $status;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('purchase_order_id', $orderInstance)->get();
                    foreach($prpod as $detail){
                        $detail->status = $status;
                        $detail->save();
                    }

                    if ($firstIteration) {
                        $firstIteration = false;
                        $supplier_id = $prpo->supplier_id;
                    }
                }

                $invoice = Invoice::create([
                    'id' => GenerateIdController::generateID('invoice'),
                    'order_id' => $id,
                    'delivery_no' => null,
                    'total_amount' => $pr->amount,
                    'date_received' => null,
                    'date_approved' => now(),
                    'supplier_id' => $supplier_id,
                    'approved_by_id' => auth('')->user()->id,
                    'status' => 13,
                ]);

                $invoice->save();

                $pr->remarks = 'order accepted';
                $pr->invoice_id = $invoice->id;
                $pr->status = $status;
                $pr->save();


                DB::commit();
                return response()->json(['success' => true, 'message' => 'Purchase Order Accepted!'], 200);
            }
            // REMOVED STATUS 16 BLOCK - It is now handled by receiveDelivery()
            else if ($status == 19) {
                $pr->remarks = $request->remarks;
                $pr->status = $status;
                $pr->save();

                $orderInstances = PurchaseOrder::where('purchase_request_id', $id)->pluck('id');

                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->delivery_date = now();
                    $prpo->remarks = $request->remarks;
                    $prpo->status = $status;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('purchase_order_id', $orderInstance)->get();
                    foreach($prpod as $detail){
                        $detail->status = $status;
                        $detail->save();
                    }
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Purchase Order Rejected!'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * NEW METHOD
     * Get all purchase order detail items for a given Purchase Request ID.
     * This is used to populate the "Receive Delivery" modal.
     */
    public function getDeliveryDetailsForModal($id)
    {
        try {
            $pr = PurchaseRequest::findOrFail($id);

            // Get all PODetail items associated with this PR
            $items = PurchaseOrderDetail::whereIn('purchase_order_id', $pr->purchaseOrders->pluck('id'))
                ->with('itemss.unitRS')
                ->get();

            if ($items->isEmpty()) {
                return response()->json(['error' => 'No items found for this purchase request.'], 404);
            }

            $mappedItems = $items->map(function ($detail) {
                return [
                    'pod_id'            => $detail->id,
                    'item_name'         => optional($detail->itemss)->name ?? 'Unknown Item',
                    'quantity_ordered'  => (int)$detail->quantity,
                    'item_unit'         => optional(optional($detail->itemss)->unitRS)->abbreviation ?? 'pcs',
                ];
            });

            return response()->json(['data' => $mappedItems]);
        } catch (\Exception $e) {
            Log::error('Error in getDeliveryDetailsForModal: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * NEW METHOD
     * Process the "Receive Delivery" modal submission.
     * Handles file uploads and updates status for PR, POs, and PODs.
     */
    public function receiveDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pr_id' => 'required|exists:purchase_requests,id',
            'overall_delivery_photo' => 'required|image|max:5120', // 5MB Max
            'items' => 'required|array|min:1',
            'items.*.pod_id' => 'required|exists:purchase_order_details,id',
            'items.*.status' => 'required|in:received,returned',
            'items.*.return_reason' => 'required_if:items.*.status,returned',
            'items.*.return_photo' => 'nullable|image|max:5120', // 5MB Max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $pr_id = $request->pr_id;
            $pr = PurchaseRequest::findOrFail($pr_id);
            $invoice = Invoice::findOrFail($pr->invoice_id);

            // 1. Handle Overall Photo
            $overallPath = $request->file('overall_delivery_photo')->store('public/delivery_proof');
            $invoice->overall_photo_path = Storage::url($overallPath); // Store public URL
            $invoice->date_received = now();
            $invoice->delivery_no = GenerateIdController::generateID('delivery_no');
            $invoice->save();


            $receivedCount = 0;
            $returnedCount = 0;
            $totalItems = count($request->items);

            // 2. Loop through submitted items
            foreach ($request->items as $index => $itemData) {
                $pod = PurchaseOrderDetail::findOrFail($itemData['pod_id']);

                if ($itemData['status'] == 'received') {
                    $pod->status = 16; // Delivered
                    $pod->delivered_qnty = $pod->quantity;
                    $pod->save();
                    $receivedCount++;

                } else if ($itemData['status'] == 'returned') {
                    $pod->status = 22; // Return
                    $pod->delivered_qnty = 0; // None were accepted
                    $pod->save();
                    $returnedCount++;

                    // Handle return photo and reason
                    $returnPhotoPath = null;
                    if ($request->hasFile("items.{$index}.return_photo")) {
                        $path = $request->file("items.{$index}.return_photo")->store('public/return_proof');
                        $returnPhotoPath = Storage::url($path); // Store public URL
                    }

                    DeliveryReturn::create([
                        'purchase_order_detail_id' => $pod->id,
                        'reason' => $itemData['return_reason'],
                        'photo_path' => $returnPhotoPath,
                    ]);
                }
            }

            // 3. Determine Overall Status
            $newStatus = null;
            $newRemarks = '';

            if ($returnedCount == 0 && $receivedCount > 0) {
                $newStatus = 16; // Delivered
                $newRemarks = 'All items received successfully.';
            } else if ($receivedCount > 0 && $returnedCount > 0) {
                $newStatus = 17; // Partial Delivered
                $newRemarks = "Partially received. {$receivedCount} items accepted, {$returnedCount} items returned.";
            } else if ($receivedCount == 0 && $returnedCount > 0) {
                $newStatus = 22; // Return
                $newRemarks = 'All items returned to supplier.';
            } else {
                 $newStatus = $pr->status; // Should not happen if items > 0
                 $newRemarks = 'No items processed.';
            }

            // 4. Update PR and all associated POs
            $pr->status = $newStatus;
            $pr->remarks = $newRemarks;
            $pr->save();

            PurchaseOrder::where('purchase_request_id', $pr_id)
                ->update([
                    'status' => $newStatus,
                    'remarks' => $newRemarks,
                    'delivery_date' => now()
                ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Delivery processed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in receiveDelivery: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
