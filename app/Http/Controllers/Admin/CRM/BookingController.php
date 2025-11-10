<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use App\Models\Status;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Mail\BookingStatusMail;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\TableForReservation;
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

    public function approve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'table_id' => ['required', 'exists:table_for_reservations,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $booking = Booking::findOrFail($validated['booking_id']);

            if ((int)$booking->status !== 11) {
                return response()->json(['error' => 'Only pending bookings can be approved.'], 422);
            }

            $table = TableForReservation::findOrFail($validated['table_id']);

            if ((int)$table->status !== 1) {
                return response()->json(['error' => 'The selected table is not available.'], 422);
            }

            if ($table->capacity < $booking->people) {
                return response()->json(['error' => 'The selected table cannot accommodate the number of guests.'], 422);
            }

            $activeStatuses = [11, 13, 23];
            $existingCount = Booking::whereDate('date', $booking->date)
                ->where('time', $booking->time)
                ->where('table_id', $table->id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', $activeStatuses)
                ->count();

            if ($existingCount >= $table->quantity) {
                return response()->json(['error' => 'The selected table is fully booked for the chosen schedule.'], 422);
            }

            $booking->table_id = $table->id;
            $booking->status = 13;
            $booking->status_note = $validated['note'];
            $booking->save();

            Mail::to($booking->email)->send(new BookingStatusMail($booking));

            return response()->json(['success' => 'Booking approved successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to approve booking: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $booking = Booking::findOrFail($validated['booking_id']);

            if ((int)$booking->status !== 11) {
                return response()->json(['error' => 'Only pending bookings can be rejected.'], 422);
            }

            $booking->status = 12;
            $booking->status_note = $validated['reason'];
            $booking->save();

            Mail::to($booking->email)->send(new BookingStatusMail($booking));

            return response()->json(['success' => 'Booking rejected successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to reject booking: ' . $e->getMessage()], 500);
        }
    }

    public function void(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $booking = Booking::findOrFail($validated['booking_id']);

            if ((int)$booking->status !== 13) {
                return response()->json(['error' => 'Only approved bookings can be voided.'], 422);
            }

            $booking->status = 31;
            $booking->status_note = $validated['reason'];
            $booking->save();

            Mail::to($booking->email)->send(new BookingStatusMail($booking));

            return response()->json(['success' => 'Booking voided successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to void booking: ' . $e->getMessage()], 500);
        }
    }
}
