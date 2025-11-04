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
        // --- 1. KPI Card Data ---
        $pendingPR = PurchaseRequest::where('status', 11)->count();
        $pendingPO = PurchaseOrder::where('status', 11)->count();
        $activeSuppliers = Supplier::where('status', 1)->count();

        // Total spend this month (status 23 = Completed)
        // This joins POs and their Details to sum the correct amounts
        $totalSpend = DB::table('purchase_orders')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->where('purchase_orders.status', 23)
            ->whereMonth('purchase_orders.created_at', Carbon::now()->month)
            ->sum('purchase_order_details.total_amount');

        // --- 2. Recent Pending Purchase Requests (PRs) ---
        $recentPendingPRs = PurchaseRequest::where('status', 11)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($pr) {
                $pr->status_html = Status::getStatusText($pr->status);
                return $pr;
            });

        // --- 3. Purchase Orders by Status (Doughnut Chart) ---
        $poByStatus = PurchaseOrder::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($po) {
                $po->status_label = strip_tags(Status::getStatusText($po->status));
                return $po;
            });

        // --- 4. Top 5 Suppliers by PO Value (Bar Chart) ---
        // This joins Suppliers, POs, and PO Details
        $topSuppliers = DB::table('suppliers')
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->select('suppliers.name as supplier_name', DB::raw('SUM(purchase_order_details.total_amount) as total'))
            ->where('purchase_orders.status', 23) // Only count 'Completed' orders
            ->groupBy('suppliers.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'kpis' => [
                'pendingPR' => $pendingPR,
                'pendingPO' => $pendingPO,
                'activeSuppliers' => $activeSuppliers,
                'totalSpend' => number_format($totalSpend, 2),
            ],
            'recentPendingPRs' => $recentPendingPRs,
            'poByStatus' => $poByStatus,
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
