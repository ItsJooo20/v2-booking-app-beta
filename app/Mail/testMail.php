<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class testMail extends Mailable
{
     use Queueable, SerializesModels;

    public $booking;
    public $user;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->user = $booking->user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Booking Created - ' . $this->booking->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.testemail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
