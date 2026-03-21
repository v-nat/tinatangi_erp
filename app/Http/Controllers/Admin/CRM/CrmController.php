<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Status;
use App\Models\Booking;
use App\Models\Announcement;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ServiceFeedback;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CrmController extends Controller
{
    public function index()
    {
        return view('pages.admin.crm.index');
    }
    public function serviceFeedback()
    {
        return view('pages.admin.crm.feedback-moderation');
    }

    public function tableManagement()
    {
        $statuses = \App\Models\Status::whereIn('id', [1, 2])->get();
        return view('pages.admin.crm.table-management', compact('statuses'));
    }

    public function getDashboardAnalytics()
    {
        $today = Carbon::today();
        $last30Days = $today->copy()->subDays(29);

        $upcomingBookingsCount = Booking::whereDate('date', '>=', $today)
            ->whereIn('status', [13, 23])
            ->count();

        $pendingBookingsCount = Booking::where('status', 11)->count();

        $pendingFeedbackCount = ServiceFeedback::where('status', 34)
            ->whereColumn('created_at', 'updated_at')
            ->count();

        $averageRating = ServiceFeedback::avg('overall_rating');

        $upcomingBookings = Booking::with('tableForReservation')
            ->whereDate('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'name' => $booking->name,
                    'people' => (int) $booking->people,
                    'date' => $booking->date ? Carbon::parse($booking->date)->format('Y-m-d') : null,
                    'time' => $booking->time,
                    'table' => $booking->tableForReservation->name ?? 'Not assigned',
                    'status' => $booking->status,
                    'status_badge' => Status::getStatusText($booking->status),
                ];
            });

        $bookingsTrend = Booking::select(
            DB::raw('date as booking_date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereDate('date', '>=', $last30Days)
            ->groupBy('booking_date')
            ->orderBy('booking_date', 'asc')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->booking_date,
                    'count' => (int) $row->count,
                ];
            });

        $statusLabels = Status::whereIn('id', [11, 12, 13, 23, 31])
            ->pluck('status', 'id');

        $statusBreakdown = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($row) use ($statusLabels) {
                return [
                    'status' => (int) $row->status,
                    'label' => $statusLabels->get($row->status, 'Unknown'),
                    'count' => (int) $row->count,
                ];
            });

        $ratingsDistribution = ServiceFeedback::select(
            DB::raw('FLOOR(overall_rating) as rating_group'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('rating_group')
            ->orderBy('rating_group', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'rating_label' => (int) $item->rating_group . ' Star' . ($item->rating_group == 1 ? '' : 's'),
                    'count' => (int) $item->count,
                ];
            });

        $categoryRatings = [
            'food' => (float) ServiceFeedback::avg('food_rating'),
            'staff' => (float) ServiceFeedback::avg('staff_rating'),
            'environment' => (float) ServiceFeedback::avg('environment_rating'),
        ];

        $topRatedFeedback = ServiceFeedback::whereNotNull('message')
            ->where('status', 35)
            ->orderByDesc('overall_rating')
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['name', 'message', 'overall_rating', 'created_at']);

        $recentFeedback = ServiceFeedback::whereNotNull('message')
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['name', 'message', 'overall_rating', 'created_at']);

        $activeAnnouncementsCount = Announcement::where('status', 35)
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_until')
                  ->orWhereDate('valid_until', '>=', $today);
            })
            ->count();

        $historicalStart = $today->copy()->subWeeks(12);
        $forecastDays = [];
        for ($i = 1; $i <= 7; $i++) {
            $forecastDate = $today->copy()->addDays($i);
            $mysqlDow = $forecastDate->dayOfWeek + 1;

            $historicalCounts = Booking::selectRaw('DATE(date) as bdate, COUNT(*) as cnt')
                ->whereDate('date', '>=', $historicalStart)
                ->whereDate('date', '<', $today)
                ->whereRaw('DAYOFWEEK(date) = ?', [$mysqlDow])
                ->groupBy('bdate')
                ->orderBy('bdate', 'asc')
                ->pluck('cnt')
                ->map(fn ($v) => (int) $v)
                ->toArray();

            $n = count($historicalCounts);
            if ($n === 0) {
                $predicted = 0.0;
            } else {
                $totalWeight = 0;
                $weightedSum = 0.0;
                foreach ($historicalCounts as $idx => $count) {
                    $weight = $idx + 1;
                    $weightedSum += $count * $weight;
                    $totalWeight += $weight;
                }
                $predicted = round($weightedSum / $totalWeight, 1);
            }

            $forecastDays[] = [
                'date'      => $forecastDate->format('Y-m-d'),
                'label'     => $forecastDate->format('D, M j'),
                'predicted' => $predicted,
            ];
        }

        $recentActualRaw = Booking::selectRaw('DATE(date) as bdate, COUNT(*) as cnt')
            ->whereDate('date', '>=', $today->copy()->subDays(13))
            ->whereDate('date', '<', $today)
            ->groupBy('bdate')
            ->pluck('cnt', 'bdate')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $recentActual = [];
        for ($i = 13; $i >= 1; $i--) {
            $d = $today->copy()->subDays($i)->format('Y-m-d');
            $recentActual[] = ['date' => $d, 'count' => $recentActualRaw[$d] ?? 0];
        }

        return response()->json([
            'kpis' => [
                'upcomingBookings'    => $upcomingBookingsCount,
                'pendingBookings'     => $pendingBookingsCount,
                'pendingFeedback'     => $pendingFeedbackCount,
                'averageRating'       => $averageRating ? round($averageRating, 2) : null,
                'activeAnnouncements' => $activeAnnouncementsCount,
            ],
            'bookingsTrend'     => $bookingsTrend,
            'statusBreakdown'   => $statusBreakdown,
            'upcomingBookings'  => $upcomingBookings,
            'categoryRatings'   => $categoryRatings,
            'ratingsDistribution' => $ratingsDistribution,
            'topRatedFeedback'  => $topRatedFeedback,
            'recentFeedback'    => $recentFeedback,
            'forecast'          => $forecastDays,
            'recentActual'      => $recentActual,
        ]);
    }

    public function getPublicProducts()
    {
        try {
            $products = Product::where('deleted_at', null)
                ->with('productCategoryRS:id,name')
                ->select('id', 'name', 'base_price', 'image', 'description', 'product_category_id')
                ->orderBy('name', 'asc')
                ->get();

            $products = $products->map(function ($product) {
                $categoryName = $product->productCategoryRS->name ?? 'Uncategorized';

                $product->filter_class = 'filter-' . Str::slug($categoryName, '-');
                $product->category_name = $categoryName;

                $product->description = $product->description ?? 'A delicious menu item.';

                unset($product->productCategoryRS);
                return $product;
            });

            return response()->json(['data' => $products]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPublicFaqs()
    {
        $faqs = Faq::where('status', 35)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json(['data' => $faqs]);
    }

    public function fetchPublicTestimonials()
    {
        $testimonials = ServiceFeedback::whereNotNull('message')
            ->where('message', '!=', '')
            ->where('status', 35)
            ->latest()
            ->limit(10)
            ->select('name', 'message', 'photo')
            ->get();

        return response()->json($testimonials);
    }
    
}
