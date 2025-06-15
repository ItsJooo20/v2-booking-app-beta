<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Services\FacilityService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\ApproveBookingRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;
    protected $bookingService;
    protected $facilityService;

    public function __construct(BookingService $bookingService, FacilityService $facilityService)
    {
        $this->bookingService = $bookingService;
        $this->facilityService = $facilityService;
    }

    public function index()
    {
        $bookings = $this->bookingService->getAllBookings();
        $calendarBookings = $this->bookingService->formatForCalendar($bookings);
        
        $upcomingBookings = Auth::user()
            ? $this->bookingService->getUpcomingBookings()
            : $this->bookingService->getUserUpcomingBookings(Auth::id());
        
        return view('admin.bookings-index', compact('bookings', 'calendarBookings', 'upcomingBookings'));
    }

    public function create()
    {
        $facilities = $this->facilityService->getAllFacilitiesWithCategory();
        $facilityItems = $this->facilityService->getAllItemsWithFacility();
        
        return view('admin.bookings-create', compact('facilities', 'facilityItems'));
    }

    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        if (!$this->bookingService->checkAvailability(
            $data['facility_item_id'],
            $data['start_datetime'],
            $data['end_datetime']
        )) {
            return redirect()->back()
                ->with('error', 'This facility is already reserved for the selected time slot.')
                ->withInput();
        }

        $this->bookingService->createBooking($data);
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking request submitted successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'facilityItem.facility']);
        return view('admin.bookings-show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $facilities = $this->facilityService->getAllFacilitiesWithItems();
        return view('admin.bookings-edit', compact('booking', 'facilities'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $data = $request->validated();

        if (!$this->bookingService->checkAvailability(
            $data['facility_item_id'],
            $data['start_datetime'],
            $data['end_datetime'],
            $booking->id
        )) {
            return redirect()->back()
                ->with('error', 'This facility is already reserved for the selected time slot.')
                ->withInput();
        }

        $this->bookingService->updateBooking($booking, $data);
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        $this->bookingService->cancelBooking($booking);
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function approve(ApproveBookingRequest $request, Booking $booking)
    {
        if (!$this->bookingService->checkAvailability(
            $booking->facility_item_id,
            $booking->start_datetime,
            $booking->end_datetime,
            $booking->id
        )) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'This timeslot is no longer available.');
        }

        $result = $this->bookingService->approveBooking($booking);
        
        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking approved successfully. ' . 
                $result['rejected_count'] . ' conflicting bookings were rejected.');
    }

    public function reject(Booking $booking)
    {
        $this->authorize('approve', $booking);
        
        $this->bookingService->rejectBooking($booking);
        
        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking rejected successfully.');
    }
}