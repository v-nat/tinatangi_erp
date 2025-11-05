<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Status;
use App\Models\PurchaseOrderDetail; // Added
use App\Models\DeliveryReturn; // Added
use Illuminate\Http\Request; // Added
use Illuminate\Support\Facades\DB; // Added
use Illuminate\Support\Facades\Log; // Added
use Illuminate\Support\Facades\Validator; // Added

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
            // Added status 17 (Partial Delivered) and 22 (Return)
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('supplier_id', auth('')->id())
                ->whereIn('status', [20, 21, 16, 19, 23, 17, 22, 36]) // Added 17 and 22
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
                        'id'             => $request_data->id,
                        'type'           => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::getStatusText($request_data->status),
                        'total_amount'   => (float)$request_data->amount,
                        'invoice_id'     => $request_data->invoice_id,
                        'supplier_name'     => optional($request_data->supplierRS)->supplier_name,
                        'purchase_orders' => $mappedOrders,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * NEW METHOD
     * Get all returned items for a specific Purchase Request.
     */
    public function getReturnDetails($id)
    {
        try {
            $pr = PurchaseRequest::findOrFail($id);

            // Find all PurchaseOrderDetail IDs associated with this PR that have a status of 22 (Return)
            $returnedPodIds = PurchaseOrderDetail::whereIn('purchase_order_id', $pr->purchaseOrders->pluck('id'))
                ->where('status', 22)
                ->pluck('id');

            // Get the DeliveryReturn details for those PODs
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $pr_id = $request->pr_id;
            $pr = PurchaseRequest::findOrFail($pr_id);

            foreach ($request->items as $itemData) {
                $pod = PurchaseOrderDetail::findOrFail($itemData['pod_id']);
                $returnRecord = DeliveryReturn::where('purchase_order_detail_id', $pod->id)->first();

                if ($itemData['action'] == 'redeliver') {
                    $pod->status = 36;
                    $pod->save();
                } else if ($itemData['action'] == 'cancel') {
                    $pod->status = 37;
                    $pod->save();
                }

                if ($returnRecord) {
                    $returnRecord->delete();
                }
            }

            $allPodStatuses = PurchaseOrderDetail::whereIn('purchase_order_id', $pr->purchaseOrders->pluck('id'))
                ->pluck('status')
                ->all();

            $newStatus = null;
            $newRemarks = '';

            if (in_array(36, $allPodStatuses)) {
                $newStatus = 36;
                $newRemarks = 'Processing returns for redelivery.';
            } else {
                $allCompleted = collect($allPodStatuses)->every(function ($status) {
                    return in_array($status, [36, 37]);
                });

                if ($allCompleted) {
                    $newStatus = 23;
                    $newRemarks = 'Order completed, returns processed.';
                } else {
                    $newStatus = 17;
                    $newRemarks = 'Returns processed.';
                }
            }

            $pr->status = $newStatus;
            $pr->remarks = $newRemarks;
            $pr->save();

            $pr->purchaseOrders()->update([
                'status' => $newStatus,
                'remarks' => $newRemarks
            ]);


            DB::commit();

            return response()->json(['success' => true, 'message' => 'Return actions processed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
