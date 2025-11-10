<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Status;
use App\Models\PurchaseOrderDetail;
use App\Models\DeliveryReturn;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemUnit;
use App\Models\PurchaseOrder;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

    public function productsPage()
    {
        return view('pages.supplier.products');
    }

    public function dashboardSummary()
    {
        $supplierId = auth('')->id();

        $totalProducts = Item::where('supplier_id', $supplierId)->count();

        $openStatuses = [20, 21, 16, 19, 17, 22, 36];
        $activeOrders = PurchaseRequest::where('supplier_id', $supplierId)
            ->whereIn('status', $openStatuses)
            ->count();

        $pendingShipments = PurchaseRequest::where('supplier_id', $supplierId)
            ->whereIn('status', [20, 21])
            ->count();

        $returnsPending = PurchaseOrderDetail::whereHas('purchaseOrder', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->where('status', 22)->count();

        $redeliveryPending = PurchaseOrderDetail::whereHas('purchaseOrder', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->where('status', 36)->count();

        $statusBreakdown = PurchaseRequest::select('status', DB::raw('COUNT(*) as total'))
            ->where('supplier_id', $supplierId)
            ->whereIn('status', [20, 21, 22, 36, 17, 16])
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                $statusHtml = Status::getStatusText($row->status);
                $statusPlain = trim(preg_replace('/\s+/', ' ', strip_tags(str_replace('<br>', ' ', $statusHtml))));
                return [
                    'status' => (int) $row->status,
                    'label' => $statusPlain,
                    'status_html' => $statusHtml,
                    'count' => (int) $row->total,
                ];
            })
            ->values();

        $recentOrders = PurchaseRequest::with('statusRS')
            ->where('supplier_id', $supplierId)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($request) {
                $statusHtml = Status::getStatusText($request->status);
                $statusPlain = trim(preg_replace('/\s+/', ' ', strip_tags(str_replace('<br>', ' ', $statusHtml))));
                $requestedDate = $request->requested_date
                    ? Carbon::parse($request->requested_date)->format('M d, Y')
                    : '—';
                return [
                    'purchase_request_id' => $request->id,
                    'status_html' => $statusHtml,
                    'status_plain' => $statusPlain,
                    'requested_date' => $requestedDate,
                    'updated_human' => optional($request->updated_at)->diffForHumans(),
                    'total_amount' => (float) $request->amount,
                ];
            })
            ->values();

        $upcomingDeliveries = PurchaseOrder::with('purchaseRequest')
            ->where('supplier_id', $supplierId)
            ->whereIn('status', [18, 21, 20, 36])
            ->orderByRaw('COALESCE(expected_delivery_date, order_date, updated_at) ASC')
            ->take(5)
            ->get()
            ->map(function ($order) {
                $statusHtml = Status::getStatusText($order->status);
                $expectedDate = $order->expected_delivery_date
                    ? Carbon::parse($order->expected_delivery_date)->format('M d, Y')
                    : 'Not set';
                return [
                    'purchase_order_id' => $order->purchase_orderId,
                    'status_html' => $statusHtml,
                    'expected_date' => $expectedDate,
                    'order_reference' => optional($order->purchaseRequest)->id,
                ];
            })
            ->values();

        $activityFeed = $recentOrders->map(function ($order) {
            return [
                'message' => "Purchase Request #{$order['purchase_request_id']} is {$order['status_plain']}",
                'timestamp' => $order['updated_human'] ?? '—',
            ];
        })->values();

        return response()->json([
            'kpis' => [
                'totalProducts' => $totalProducts,
                'activeOrders' => $activeOrders,
                'pendingShipments' => $pendingShipments,
                'returnsPending' => $returnsPending,
                'redeliveryPending' => $redeliveryPending,
            ],
            'statusBreakdown' => $statusBreakdown,
            'recentOrders' => $recentOrders,
            'upcomingDeliveries' => $upcomingDeliveries,
            'activityFeed' => $activityFeed,
        ]);
    }

    public function productOptions()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $units = ItemUnit::select('id', 'name', 'abbreviation')->orderBy('name')->get();

        return response()->json([
            'categories' => $categories,
            'units' => $units,
        ]);
    }

    public function listItems()
    {
        $supplierId = auth('')->id();

        $items = Item::with(['category', 'unit'])
            ->where('supplier_id', $supplierId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'category_name' => optional($item->category)->name,
                'unit_name' => optional($item->unit)->name,
                'unit_price' => (float) $item->unit_price,
                'unit_price_formatted' => number_format($item->unit_price, 2),
                'status' => $item->status,
                'status_html' => $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>',
                'updated_at' => optional($item->updated_at)->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeItem(StoreItemRequest $request)
    {
        $supplierId = auth('')->id();

        $item = Item::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'inventory_location_id' => $request->inventory_location_id ?? null,
            'unit_price' => $request->unit_price,
            'status' => $request->status ?? 1,
            'supplier_id' => $supplierId,
        ]);

        return response()->json([
            'message' => 'Product created successfully!',
            'data' => $item->id,
        ], 201);
    }

    public function showItem(Item $item)
    {
        $this->authorizeItem($item);

        return response()->json([
            'data' => [
                'id' => $item->id,
                'name' => $item->name,
                'category_id' => $item->category_id,
                'unit_id' => $item->unit_id,
                'inventory_location_id' => $item->inventory_location_id,
                'unit_price' => $item->unit_price,
                'status' => $item->status,
            ],
        ]);
    }

    public function updateItem(UpdateItemRequest $request, Item $item)
    {
        $this->authorizeItem($item);

        $item->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'inventory_location_id' => $request->inventory_location_id ?? null,
            'unit_price' => $request->unit_price,
            'status' => $request->status ?? $item->status,
        ]);

        return response()->json(['message' => 'Product updated successfully!']);
    }

    protected function authorizeItem(Item $item): void
    {
        if ($item->supplier_id !== auth('')->id()) {
            abort(403, 'Unauthorized action.');
        }
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
