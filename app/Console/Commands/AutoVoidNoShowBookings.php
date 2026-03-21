<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Booking;
use App\Mail\BookingStatusMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AutoVoidNoShowBookings extends Command
{
    protected $signature   = 'bookings:auto-void-no-shows';
    protected $description = 'Auto-void approved bookings where the customer did not arrive within 15 minutes';

    public function handle(): void
    {
        $now      = Carbon::now('Asia/Manila');
        $today    = $now->toDateString();
        $cutoffTs = $now->copy()->subMinutes(15);

        $noShows = Booking::where('date', $today)
            ->where('status', 13)
            ->whereNotNull('table_number')
            ->get()
            ->filter(function ($booking) use ($cutoffTs, $today) {
                $bookingStart = Carbon::parse($today . ' ' . $booking->time, 'Asia/Manila');
                return $bookingStart->lte($cutoffTs);
            });

        foreach ($noShows as $booking) {
            $booking->status      = 31;
            $booking->status_note = 'Auto-voided: customer did not arrive within 15 minutes.';
            $booking->save();

            try {
                Mail::to($booking->email)->send(new BookingStatusMail($booking));
            } catch (\Exception $e) {
            }
        }

        $this->info("Auto-voided {$noShows->count()} no-show booking(s).");
    }
}
