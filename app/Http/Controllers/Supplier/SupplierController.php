<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Status;
use App\Models\PurchaseOrderDetail;
use App\Models\DeliveryReturn;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function approveRequest()
    {
        return view('pages.supplier.approve-purchase');
    }
    public function index()
    {
        return view('pages.supplier.index');
    }

    public function purchaseOrdersList()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('supplier_id', auth('')->id())
                ->whereIn('status', [20, 21, 16, 19, 23, 17, 22, 36])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,

                        ];
                    });

                    return [
                        'id'            => $request_data->id,
                        'type'            => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'         => $request_data->remarks,
                        'status'          => Status::getStatusText($request_data->status),
                        'total_amount'    => (float)$request_data->amount,
                        'invoice_id'      => $request_data->invoice_id,
                        'supplier_name'     => optional($request_data->supplierRS)->supplier_name,
                        'purchase_orders' => $mappedOrders,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getReturnDetails($id)
    {
        try {
            $pr = PurchaseRequest::findOrFail($id);

            $returnedPodIds = PurchaseOrderDetail::whereIn('purchase_order_id', $pr->purchaseOrders->pluck('id'))
                ->where('status', 22)
                ->pluck('id');

            $returns = DeliveryReturn::whereIn('purchase_order_detail_id', $returnedPodIds)
                ->with('purchaseOrderDetail.itemss')
                ->get();

            if ($returns->isEmpty()) {
                return response()->json(['error' => 'No returned items found for this order.'], 404);
            }

            $mappedReturns = $returns->map(function ($return) {
                return [
                    'pod_id' => $return->purchase_order_detail_id,
                    'item_name' => optional(optional($return->purchaseOrderDetail)->itemss)->name ?? 'Unknown Item',
                    'quantity' => optional($return->purchaseOrderDetail)->quantity ?? 0,
                    'reason' => $return->reason,
                    'photo_path' => $return->photo_path ? asset($return->photo_path) : null,
                ];
            });

            return response()->json(['data' => $mappedReturns]);
        } catch (\Exception $e) {
            Log::error('Error in getReturnDetails: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function processReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pr_id' => 'required|exists:purchase_requests,id',
            'items' => 'required|array|min:1',
            'items.*.pod_id' => 'required|exists:purchase_order_details,id',
            'items.*.action' => 'required|in:redeliver,cancel',
            'items.*.cancel_reason' => 'required_if:items.*.action,cancel|nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $pr_id = $request->pr_id;
            $pr = PurchaseRequest::findOrFail($pr_id);
            $cancelledItemsRemarks = [];
            $needsRecalculation = false;

            foreach ($request->items as $itemData) {
                $pod = PurchaseOrderDetail::with('itemss')->findOrFail($itemData['pod_id']);
                $returnRecord = DeliveryReturn::where('purchase_order_detail_id', $pod->id)->first();

                if ($itemData['action'] == 'redeliver') {
                    $pod->status = 36;
                    $pod->save();
                } else if ($itemData['action'] == 'cancel') {

                    $itemName = $pod->itemss->name ?? 'Item ID ' . $pod->item_id;
                    $reason = $itemData['cancel_reason'] ?? 'No reason provided';
                    $cancelledItemsRemarks[] = "Item '{$itemName}' (Qty: {$pod->quantity}) was CANCELLED & REMOVED. Reason: {$reason}";

                    $pod->delete();
                    $needsRecalculation = true;
                }

                if ($returnRecord) {
                    $returnRecord->delete();
                }
            }

            if ($needsRecalculation) {
                Log::info("Recalculating amounts for PR ID: {$pr->id}");

                $po_ids = $pr->purchaseOrders->pluck('id');

                $newTotalAmount = PurchaseOrderDetail::whereIn('purchase_order_id', $po_ids)
                                    ->sum('total_amount');

                $pr->amount = $newTotalAmount;

                $invoice = Invoice::where('order_id', $pr->id)->first();
                if ($invoice) {
                    $invoice->total_amount = $newTotalAmount;
                    $invoice->save();
                }
            }

            $allPods = PurchaseOrderDetail::whereIn('purchase_order_id', $pr->purchaseOrders->pluck('id'))
                                ->withTrashed()
                                ->get();

            $allPodStatuses = $allPods->map(function($pod) {
                if ($pod->trashed()) {
                    return 37;
                }
                return $pod->status;
            })->all();


            $newStatus = null;
            $newRemarks = '';

            if (in_array(36, $allPodStatuses)) {
                $newStatus = 36;
                $newRemarks = 'Processing returns. Some items pending redelivery.';
            } else {
                $allCompleted = collect($allPodStatuses)->every(function ($status) {
                    return in_array($status, [16, 37]);
                });

                if ($allCompleted) {
                    $newStatus = 16;
                    $newRemarks = 'Order completed, returns processed.';
                } else if (in_array(16, $allPodStatuses) || in_array(37, $allPodStatuses)) {
                    $newStatus = 17;
                    $newRemarks = 'Returns processed. Some items remain.';
                } else {
                    $newStatus = $pr->status;
                    $newRemarks = 'Returns processed.';

                    if (collect($allPodStatuses)->every(fn($s) => $s == 37)) {
                        $newStatus = 16;
                        $newRemarks = 'All returned items have been cancelled by supplier.';
                    }
                }
            }

            $finalRemarks = $newRemarks;
            if (!empty($cancelledItemsRemarks)) {
                $finalRemarks .= " \n\n[Cancelled Items]:\n" . implode("\n", $cancelledItemsRemarks);
            }

            $pr->status = $newStatus;
            $pr->remarks = $finalRemarks;
            $pr->save();

            $pr->purchaseOrders()->update([
                'status' => $newStatus,
                'remarks' => $finalRemarks
            ]);


            DB::commit();

            return response()->json(['success' => true, 'message' => 'Return actions processed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in processReturn: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
