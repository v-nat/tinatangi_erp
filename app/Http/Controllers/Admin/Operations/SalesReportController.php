<?php

namespace App\Http\Controllers\Admin\Operations;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Status;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Requests\StoreSalesReportRequest;

class SalesReportController extends Controller
{
    public function index()
    {
        return view('pages.admin.operations.sales-reporting');
    }

    public function listEligibleOrders(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $orders = Order::with(['orderItemsRS.productRS'])
            ->whereDate('created_at', $date)
            ->where('status', 23)
            ->where(function ($query) {
                $query->doesntHave('salesReport')
                    ->orWhereHas('salesReport', function ($sub) {
                        $sub->where('status', 12);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $orders->map(function (Order $order) {
            return [
                'id' => $order->id,
                'order_code' => $order->order_id,
                'total_amount' => (float) $order->total_amount,
                'order_type' => ucwords(str_replace('-', ' ', $order->order_type)),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->orderItemsRS->map(function ($item) {
                    return [
                        'name' => optional($item->productRS)->name ?? 'Unknown',
                        'quantity' => $item->quantity,
                        'subtotal' => (float) $item->subtotal,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function listSubmittedReports(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $reports = SalesReport::with(['orderRS', 'statusRS', 'reviewerRS'])
            ->whereDate('reported_at', $date)
            ->orderBy('reported_at', 'desc')
            ->get();

        $data = $reports->map(function (SalesReport $report) {
            return [
                'id' => $report->id,
                'order_code' => optional($report->orderRS)->order_id,
                'total_amount' => (float) $report->total_amount,
                'status' => $report->status,
                'status_html' => Status::getStatusText($report->status),
                'reported_at' => optional($report->reported_at)->format('Y-m-d H:i:s'),
                'reviewed_at' => optional($report->reviewed_at)->format('Y-m-d H:i:s'),
                'reviewed_by' => optional($report->reviewerRS)->full_name,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(StoreSalesReportRequest $request): JsonResponse
    {
        if (!AuthController::checkAuthorization()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for this action.'
            ], 401);
        }

        $orderIds = $request->input('order_ids', []);
        $remarks = $request->input('remarks');

        $orders = Order::with('salesReport')
            ->whereIn('id', $orderIds)
            ->where('status', 23)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No eligible orders found to report.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($orders as $order) {
                if ($order->salesReport && $order->salesReport->status == 23) {
                    continue;
                }

                SalesReport::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'total_amount' => $order->total_amount,
                        'status' => 11,
                        'reported_by' => auth('')->id(),
                        'reported_at' => now(),
                        'reviewed_by' => null,
                        'reviewed_at' => null,
                        'remarks' => $remarks,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Sales transactions reported to Finance successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to report sales transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
