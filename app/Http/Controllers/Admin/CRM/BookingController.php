<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
class BookingController extends Controller
{
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = 11;

        try {
            Booking::create($validated);

            return response()->json([
                'success' => 'Your booking request was sent. We will call back or send an Email to confirm your reservation. Thank you!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
