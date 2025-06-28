<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\BookingService;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatus
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->bookingService->updateExpiredBookings();
        } catch (\Exception $e) {
            Log::error('Failed to update booking statuses: ' . $e->getMessage(), [
                'exception' => $e,
                'request_path' => $request->path()
            ]);
        }

        return $next($request);
    }
}