<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use App\Models\Status;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;

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

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bookingPage()
    {
        return view('pages.admin.crm.bookings');
    }

    public function getBookings(): JsonResponse
    {
        $bookings = Booking::with('statusRS')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return response()->json(['data' => $bookings]);
    }

    public function updateBookingStatus(UpdateBookingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $booking = Booking::findOrFail($validated['booking_id']);
            $booking->status = $validated['status_id'];
            $booking->save();

            return response()->json(['success' => 'Booking status updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyBooking(Booking $booking): JsonResponse
    {
        try {
            $booking->delete();
            return response()->json(['success' => 'Booking deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete booking.'], 500);
        }
    }
}
