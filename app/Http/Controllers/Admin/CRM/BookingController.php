<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $captchaToken = $request->input('g-recaptcha-response');
        $secretKey = env('RECAPTCHA_SECRET_KEY');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secretKey,
            'response' => $captchaToken,
            'remoteip' => $request->ip()
        ]);

        $responseData = $response->json();

        if (!$responseData || !$responseData['success']) {
            return response()->json([
                'error' => 'CAPTCHA verification failed. Please try again.'
            ], 400);
        }

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
