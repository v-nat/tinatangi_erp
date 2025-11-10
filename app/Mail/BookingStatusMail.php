<?php

namespace App\Mail;

use App\Models\Booking; // <-- Add this
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The booking instance.
     *
     * @var \App\Models\Booking
     */
    public $booking;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        $subject = match ((int) $this->booking->status) {
            13 => 'Your Booking is Confirmed!',
            23 => 'Thank You for Dining with Us!',
            12 => 'Your Booking Request Status',
            31 => 'Update on Your Booking',
            default => 'Your Booking Request is Pending',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->resolveView(),
        );
    }

    protected function resolveView(): string
    {
        return match ((int) $this->booking->status) {
            13, 23 => 'emails.booking.status',
            12 => 'emails.booking.rejected',
            31 => 'emails.booking.voided',
            default => 'emails.booking.status',
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
