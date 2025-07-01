<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingApiService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ApiStoreBookingRequest;
use App\Http\Requests\ApiUpdateBookingRequest;

class BookingApiController extends Controller
{
    protected $bookingService;

    public function __construct(BookingApiService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getUserBookings($request->user());
        return $this->successResponse($bookings);
    }

    public function upcoming(Request $request)
    {
        $bookings = $this->bookingService->getUpcomingBookings($request->user());
        return $this->successResponse($bookings);
    }

    public function approvedEvents(Request $request)
    {
        $validated = $request->validate([
            'facility_item_id' => 'sometimes|nullable|exists:facility_items,id',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
        ]);

        $events = $this->bookingService->getApprovedEvents($validated);
        return $this->successResponse($events);
    }

    public function show(Booking $booking)
    {
        // $this->authorize('view', $booking);
        $booking->load(['user', 'facilityItem.facilityItemImage']);
        return $this->successResponse($booking);
    }

    public function store(ApiStoreBookingRequest $request)
    {
        $user = $request->user();

        if (!$this->bookingService->checkAvailability(
            $request->facility_item_id,
            $request->start_datetime,
            $request->end_datetime
        )) {
            return $this->errorResponse('This facility is already reserved', 409);
        }

        if ($request->has('equipment_ids')) {
            foreach ($request->equipment_ids as $equipmentId) {
                if (!$this->bookingService->checkEquipmentAvailability(
                    $equipmentId, 
                    $request->start_datetime, 
                    $request->end_datetime)) 
                {
                    return $this->errorResponse("Equipment unavailable", 409);
                }
            }
        }

        try {
            $this->bookingService->validateAddOns(
                $request->facility_item_id,
                $request->equipment_ids ?? []
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        $booking = $this->bookingService->createBooking($user, $request->validated());

        // Save add-ons
        if ($request->has('equipment_ids')) {
            foreach ($request->equipment_ids as $equipmentId) {
                \App\Models\BookingEquipmentRequest::create([
                    'booking_id' => $booking->id,
                    'facility_item_id' => $equipmentId,
                    'status' => 'pending'
                ]);
            }
        }

        // Load add-ons for response
        $booking->load(['equipmentRequests.facilityItem']);

        return $this->successResponse($booking, 'Booking created successfully', 201);
    }

    public function update(ApiUpdateBookingRequest $request, Booking $booking)
    {
        // $this->authorize('update', $booking);

        $data = $request->validated();
        
        if ($this->shouldCheckAvailability($request)) {
            $facilityItemId = $request->facility_item_id ?? $booking->facility_item_id;
            $start = $request->start_datetime ?? $booking->start_datetime;
            $end = $request->end_datetime ?? $booking->end_datetime;

            if (!$this->bookingService->checkAvailability($facilityItemId, $start, $end, $booking->id)) {
                return $this->errorResponse('This facility is already reserved', 409);
            }
        }

        $booking = $this->bookingService->updateBooking($booking, $data);
        return $this->successResponse($booking, 'Booking updated successfully');
    }

    public function destroy(Booking $booking)
    {
        // $this->authorize('delete', $booking);
        
        $booking->status = 'cancelled';
        $booking->save();
        
        return $this->successResponse(null, 'Booking cancelled successfully');
    }

    public function approve(Request $request, Booking $booking)
    {
        // $this->authorize('approve', $booking);

        if ($booking->status != 'pending') {
            return $this->errorResponse('Only pending bookings can be approved', 400);
        }

        if (!$this->bookingService->checkAvailability(
            $booking->facility_item_id,
            $booking->start_datetime,
            $booking->end_datetime,
            $booking->id
        )) {
            return $this->errorResponse('This timeslot is no longer available', 400);
        }

        $result = $this->bookingService->approveBooking($booking);
        
        return $this->successResponse(
            $result['booking'],
            'Booking approved. ' . $result['rejected_count'] . ' conflicts rejected'
        );
    }

    public function reject(Request $request, Booking $booking)
    {
        // $this->authorize('approve', $booking);

        $booking->status = 'rejected';
        $booking->save();
        
        return $this->successResponse($booking, 'Booking rejected');
    }

    public function userBookingHistory(Request $request)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:needs return,return submitted,pending,approved,rejected,completed,cancelled',
            'time_filter' => 'sometimes|in:today,week,month'
        ]);

        Log::info('Debug - Validated filters: ' . json_encode($validated));
        Log::info('Debug - Authenticated user: ' . $request->user()->id);

        $bookings = $this->bookingService->getUserBookingHistory($request->user(), $validated);
        
        Log::info('Debug - Service returned: ' . json_encode($bookings));
        
        return $this->successResponse($bookings);
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id != $request->user()->id && !$request->user()->isAdmin()) {
            return $this->errorResponse('You are not authorized to cancel this booking', 403);
        }
        
        if (in_array($booking->status, ['cancelled', 'completed', 'rejected'])) {
            return $this->errorResponse('This booking cannot be cancelled', 400);
        }
        
        $booking->status = 'cancelled';
        $booking->save();
        
        return $this->successResponse(null, 'Booking cancelled successfully');
    }

    private function shouldCheckAvailability(Request $request): bool
    {
        return $request->has('facility_item_id') || 
               $request->has('start_datetime') || 
               $request->has('end_datetime');
    }

    private function successResponse($data, string $message = '', int $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => "",
            'data' => $data
        ], $code);
    }

    private function errorResponse(string $message, int $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }


}