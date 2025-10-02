<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $user;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->user = $booking->user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking Created')
            ->line('A new booking has been created by ' . $this->user->name)
            ->line('Booking ID: ' . $this->booking->id)
            ->line('Booking Date: ' . $this->booking->booking_date)
            ->action('View Booking', url('/bookings/' . $this->booking->id))
            ->line('Thank you!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'user_name' => $this->user->name,
            'booking_date' => $this->booking->booking_date,
            'message' => 'New booking created by ' . $this->user->name,
        ];
    }
}