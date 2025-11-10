<?php

namespace App\Http\Controllers\Admin\Procurement;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProcurementController extends Controller
{
    public function index()
    {
        return view("pages.admin.procurement.index");
    }

    public function createPR()
    {
        return view("pages.admin.procurement.create-purchase-request");
    }
    public function supplier()
    {
        return view("pages.admin.procurement.manage-supplier");
    }
    public function purchaseOrders()
    {
        return view("pages.admin.procurement.purchase-orders");
    }

    public function getDashboardAnalytics()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $completedStatuses = [23];
        $closedStatuses = [23, 19];

        $pendingPRCount = PurchaseRequest::where('status', 11)->count();

        $openPOCount = PurchaseOrder::whereNotIn('status', $closedStatuses)->count();

        $overduePOCount = PurchaseOrder::whereNotIn('status', $closedStatuses)
            ->whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', $today)
            ->count();

        $monthSpend = DB::table('purchase_orders')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$monthStart, $monthEnd])
            ->sum('purchase_order_details.total_amount');

        $recentPRs = PurchaseRequest::with(['employeeRS.userRS'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->id,
                    'reference' => $pr->order_id ?? ('PR-' . str_pad($pr->id, 5, '0', STR_PAD_LEFT)),
                    'requested_at' => optional($pr->created_at)->toDateTimeString(),
                    'requested_by' => optional(optional($pr->employeeRS)->userRS)->full_name,
                    'status_html' => Status::getStatusText($pr->status),
                ];
            })
            ->values();

        $recentPOs = PurchaseOrder::with(['supplierRS', 'purchaseOrderDetail'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($po) use ($closedStatuses) {
                $totalAmount = $po->purchaseOrderDetail->sum('total_amount');
                $isOpen = !in_array($po->status, $closedStatuses, true);
                $isOverdue = $isOpen && $po->delivery_date && Carbon::parse($po->delivery_date)->isPast();

                return [
                    'id' => $po->id,
                    'reference' => $po->purchase_orderId ?? ('PO-' . str_pad($po->id, 5, '0', STR_PAD_LEFT)),
                    'order_date' => $po->order_date,
                    'delivery_date' => $po->delivery_date,
                    'supplier_name' => optional($po->supplierRS)->supplier_name,
                    'status_html' => Status::getStatusText($po->status),
                    'total_amount' => (float) $totalAmount,
                    'is_overdue' => $isOverdue,
                ];
            })
            ->values();

        $topSuppliers = DB::table('suppliers')
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$monthStart, $monthEnd])
            ->select('suppliers.supplier_name as supplier_name', DB::raw('SUM(purchase_order_details.total_amount) as total'))
            ->groupBy('suppliers.supplier_name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($row) {
                return [
                    'supplier_name' => $row->supplier_name,
                    'total' => (float) $row->total,
                ];
            })
            ->values();

        return response()->json([
            'kpis' => [
                'pendingPR' => $pendingPRCount,
                'openPO' => $openPOCount,
                'overduePO' => $overduePOCount,
                'monthSpend' => (float) $monthSpend,
            ],
            'recentPRs' => $recentPRs,
            'recentPOs' => $recentPOs,
            'topSuppliers' => $topSuppliers,
        ]);
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
            ])->whereNot('status', 11)->WhereNot('status', 27)
                ->orderBy('requested_date', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'supplier_name'     => optional($order->supplierRS)->supplier_name,
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

    public function purchaseRequestsList()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('status', 27)->orWhere('status', 11)->orWhere('status', 23)
                ->orderBy('requested_date', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'supplier_name'     => optional($order->supplierRS)->supplier_name,
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

    public function getRestockData($id)
    {
        try {
            $purchaseRequest = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.purchaseOrderDetail',
                'purchaseOrders.supplierRS',

                'purchaseOrders.purchaseOrderDetail.itemss',
                'purchaseOrders.purchaseOrderDetail.itemss.categoryRS',
                'purchaseOrders.purchaseOrderDetail.itemss.unitRS',
            ])->find($id);
            if (!$purchaseRequest) {
                return response()->json(['error' => 'Purchase Request not found.'], 404);
            }
            $mappedOrders = $purchaseRequest->purchaseOrders->map(function ($order) {

                $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                    return [
                        'item_id'       => $detail->item_id,
                        'item_name'     => optional($detail->itemss)->name,
                        'item_unit'     => optional(optional($detail->itemss)->unitRS)->name,
                        'category_id'   => $detail->category_id,

                        'category_name' => optional(optional($detail->itemss)->categoryRS)->name,

                        'quantity'      => (int)$detail->quantity,
                        'unit_price'    => (float)$detail->unit_price,
                        'total_amount'  => (float)$detail->total_amount,
                    ];
                });

                return [
                    'purchase_order_id' => $order->purchase_orderId,
                    'details'           => $mappedDetails,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id'              => $purchaseRequest->id,
                    'total_amount'    => (float)$purchaseRequest->amount,
                    'purchase_orders' => $mappedOrders,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected server error occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}
