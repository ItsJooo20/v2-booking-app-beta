<?php

namespace App\Jobs;

use App\Mail\testMail;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\BookingCreatedNotification;

class SendBookingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;
    public $adminId;
    public $tries = 5; // Retry 5 times if failed
    public $timeout = 60; // 1 minute timeout
    public $backoff = [10, 30, 60]; // Wait 10s, 30s, 60s between retries

    public function __construct(Booking $booking, $adminId)
    {
        $this->booking = $booking;
        $this->adminId = $adminId;
    }

    public function handle()
    {
        try {
            // Send notification to single admin
            $admin = User::find($this->adminId);
            
            if ($admin && $admin->email) {
                // $admin->notify(new BookingCreatedNotification($this->booking));
                Mail::to($admin->email)->send(new testMail($this->booking));
                
                Log::info("Notification sent successfully to admin: {$admin->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification to admin ID {$this->adminId}: " . $e->getMessage());
            
            // Re-throw to trigger retry
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        // Log when job finally fails after all retries
        Log::error("Job failed permanently for admin ID {$this->adminId}: " . $exception->getMessage());
    }
}