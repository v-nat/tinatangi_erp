<?php

namespace App\Http\Controllers\Admin\Finance;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Requests\ReviewSalesReportRequest;

class SalesTransactionController extends Controller
{
    public function index()
    {
        return view('pages.admin.finance.sales-transactions');
    }

    public function list(Request $request): JsonResponse
    {
        $statusFilter = $request->input('status');

        $reports = SalesReport::with(['orderRS', 'reporterRS', 'reviewerRS'])
            ->when($statusFilter, fn($q) => $q->where('status', (int) $statusFilter))
            ->orderBy('reported_at', 'desc')
            ->get();

        $data = $reports->map(function (SalesReport $report) {
            return [
                'id' => $report->id,
                'order_code' => optional($report->orderRS)->order_id ?? 'N/A',
                'order_type' => optional($report->orderRS)->order_type,
                'total_amount' => (float) $report->total_amount,
                'status' => $report->status,
                'status_html' => Status::getStatusText($report->status),
                'reported_by' => optional($report->reporterRS)->full_name ?? 'N/A',
                'reported_at' => optional($report->reported_at)->format('Y-m-d H:i:s'),
                'reviewed_by' => optional($report->reviewerRS)->full_name,
                'reviewed_at' => optional($report->reviewed_at)->format('Y-m-d H:i:s'),
                'remarks' => $report->remarks,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function review(ReviewSalesReportRequest $request): JsonResponse
    {
        if (!AuthController::checkAuthorization()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for this action.'
            ], 401);
        }
        $reportIds = $request->input('report_ids', []);
        $action = $request->input('action');
        $remarks = $request->input('remarks');

        $targetStatus = $action === 'approve' ? 23 : 12;

        DB::beginTransaction();

        try {
            $reports = SalesReport::whereIn('id', $reportIds)->get();

            foreach ($reports as $report) {
                if ($report->status !== 11) {
                    continue;
                }

                $report->status = $targetStatus;
                $report->reviewed_by = auth('')->id();
                $report->reviewed_at = now();
                $report->remarks = $remarks;
                $report->save();
            }

            DB::commit();

            return response()->json([
                'message' => $action === 'approve'
                    ? 'Sales report approved successfully.'
                    : 'Sales report marked as rejected.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process sales report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function analytics(): JsonResponse
    {
        $start = Carbon::today()->subDays(6);

        $daily = SalesReport::select(
            DB::raw("DATE(reported_at) as report_date"),
            DB::raw("SUM(total_amount) as total"),
            DB::raw("SUM(CASE WHEN status = 23 THEN total_amount ELSE 0 END) as approved_total")
        )
            ->whereNotNull('reported_at')
            ->whereDate('reported_at', '>=', $start)
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->get();

        $labels = [];
        $reportedSeries = [];
        $approvedSeries = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->format('M d');
            $match = $daily->firstWhere('report_date', $day->toDateString());
            $reportedSeries[] = $match ? (float) $match->total : 0;
            $approvedSeries[] = $match ? (float) $match->approved_total : 0;
        }

        $pendingCount = SalesReport::where('status', 11)->count();
        $approvedToday = SalesReport::where('status', 23)->whereDate('reviewed_at', Carbon::today())->sum('total_amount');

        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Reported', 'data' => $reportedSeries],
                ['name' => 'Approved', 'data' => $approvedSeries],
            ],
            'meta' => [
                'pending' => $pendingCount,
                'approved_today' => (float) $approvedToday,
            ],
        ]);
    }
}

