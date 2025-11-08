<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use App\Models\Status;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Mail\BookingStatusMail;
use Illuminate\Http\JsonResponse;
use App\Models\TableForReservation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;

class BookingController extends Controller
{
    public function checkAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => 'required',
            'people' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $date = $request->input('date');
            $time = $request->input('time');
            $people = (int)$request->input('people');

            $accomodatingTables = TableForReservation::where('capacity', '>=', $people)
                                                     ->where('status', 1)
                                                     ->get();

            if ($accomodatingTables->isEmpty()) {
                return response()->json(['error' => 'Sorry, no tables are available for that party size.'], 404);
            }

            $bookedTableIds = Booking::where('date', $date)
                                    ->where('time', $time)
                                    ->whereIn('status', [11, 13])
                                    ->pluck('table_id')
                                    ->toArray();

            $finalAvailableTables = [];
            foreach ($accomodatingTables as $table) {
                $bookedCount = count(array_keys($bookedTableIds, $table->id));
                if ($bookedCount < $table->quantity) {
                    $finalAvailableTables[] = $table;
                }
            }

            if (empty($finalAvailableTables)) {
                return response()->json(['error' => 'Sorry, all tables for that time and party size are fully booked.'], 404);
            }

            return response()->json(['tables' => $finalAvailableTables]);

        } catch (Exception $e) {
            return response()->json(['error' => 'An server error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $people = (int)$validated['people'];
        if ($people < 6) {
            $validated['status'] = 13;
        } else {
            $validated['status'] = 11;
        }

        try {
            $booking = Booking::create($validated);
            Mail::to($booking->email)->send(new BookingStatusMail($booking));

            $message = $validated['status'] == 13
                ? 'Your booking has been confirmed! We look forward to seeing you.'
                : 'Your booking request was sent. We will call back or send an Email to confirm your reservation. Thank you!';

            return response()->json(['success' => $message], 200);

        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json(['error' => 'This exact time slot seems to be already taken. Please try another time.'], 409);
            }
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function bookingPage()
    {
        return view('pages.admin.crm.bookings');
    }

    public function getBookings(): JsonResponse
    {
        $bookings = Booking::with(['statusRS', 'tableForReservation'])
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
