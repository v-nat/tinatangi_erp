<?php

namespace App\Http\Controllers;

use App\Services\BestSellerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BestSellerController extends Controller
{
    protected BestSellerService $bestSellerService;

    public function __construct(BestSellerService $bestSellerService)
    {
        $this->bestSellerService = $bestSellerService;
    }

    public function publicHighlights(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 3);
        if ($limit <= 0) {
            $limit = 3;
        }
        $limit = min($limit, 5);

        $weekly = $this->bestSellerService->getWeeklyBestSellers($limit);
        $monthly = $this->bestSellerService->getMonthlyBestSellers($limit);

        $featuredProductIds = collect([$weekly, $monthly])
            ->flatMap(function ($period) {
                return collect($period['categories'] ?? [])
                    ->flatMap(fn ($category) => collect($category['items'] ?? [])->pluck('product_id'));
            })
            ->unique()
            ->values()
            ->all();

        $weeklyTrend = $this->bestSellerService->getProductTrend($featuredProductIds, 'weekly', 6);

        return response()->json([
            'weekly' => $weekly,
            'monthly' => $monthly,
            'trends' => [
                'weekly' => $weeklyTrend,
            ],
            'generated_at' => Carbon::now()->toIso8601String(),
        ]);
    }
}




