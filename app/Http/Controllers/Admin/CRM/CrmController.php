<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Status;
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

    public function getDashboardAnalytics()
    {
        $totalFeedback = ServiceFeedback::count();
        $averageRating = ServiceFeedback::avg('overall_rating');
        $pendingCount = ServiceFeedback::where('status', 34)->whereColumn('created_at', 'updated_at')->count();
        $displayedCount = ServiceFeedback::where('status', 35)->count();

        $recentPending = ServiceFeedback::where('status', 34)->whereColumn('created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($feedback) {
                $feedback->status_html = Status::getStatusText($feedback->status);
                return $feedback;
            });

        $ratingsDistribution = ServiceFeedback::select(
            DB::raw('FLOOR(overall_rating) as rating_group'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('rating_group')
            ->orderBy('rating_group', 'asc')
            ->get()
            ->map(function ($item) {
                $item->rating_label = (int)$item->rating_group . ' Star' . ($item->rating_group == 1 ? '' : 's');
                return $item;
            });

        $categoryRatings = [
            'food' => ServiceFeedback::avg('food_rating'),
            'staff' => ServiceFeedback::avg('staff_rating'),
            'environment' => ServiceFeedback::avg('environment_rating'),
        ];

        $feedbackOverTime = ServiceFeedback::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'kpis' => [
                'totalFeedback' => $totalFeedback,
                'averageRating' => number_format($averageRating, 2),
                'pendingCount' => $pendingCount,
                'displayedCount' => $displayedCount,
            ],
            'recentPending' => $recentPending,
            'ratingsDistribution' => $ratingsDistribution,
            'categoryRatings' => $categoryRatings,
            'feedbackOverTime' => $feedbackOverTime,
        ]);
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
