<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Mail\testMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBookingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;
    public $admins;

    public function __construct($booking, $admins)
    {
        $this->booking = $booking;
        // $this->admins = $admins;
        $this->admins = $admins->pluck('email')->toArray();


        Log::info('SendBookingEmail job created', [
            'booking_id' => $booking->id,
            // 'admins' => $admins->pluck('email')->toArray(),
            'admins' => $this->admins = $admins,
        ]);
    }

    public function handle()
    {
        Log::info('SendBookingEmail running', ['booking_id' => $this->booking->id]);

        try {
            foreach ($this->admins as $admin) {
                Mail::to($admin->email)->send(new testMail($this->booking));
                Log::info("Email sent to {$admin->email}");
            }
        } catch (\Throwable $e) {
            Log::error("SendBookingEmail failed: ".$e->getMessage());
        }

        Log::info('SendBookingEmail finished', ['booking_id' => $this->booking->id]);
    }
}
