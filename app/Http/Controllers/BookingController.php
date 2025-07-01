<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\EquipmentReturn;
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
            : $this->bookingService->getUserUpcomingBookings(Auth::id())->paginate(3);
        
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
        // $this->authorize('update', $booking);
        
        $facilities = $this->facilityService->getAllFacilitiesWithItems();
        return view('admin.bookings-edit', compact('booking', 'facilities'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        // $this->authorize('update', $booking);
        
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
        // $this->authorize('delete', $booking);
        
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
        // $this->authorize('approve', $booking);
        
        $this->bookingService->rejectBooking($booking);
        
        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking rejected successfully.');
    }

    // Add these methods to your existing BookingController

public function showReturnForm(Booking $booking)
{
    // Check if user is allowed to submit return
    if ($booking->user_id != Auth::id() && !in_array(Auth::user()->role, ['admin', 'headmaster'])) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'You are not authorized to submit a return for this booking.');
    }
    
    // Check if return already exists
    if ($booking->equipmentReturn) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'A return has already been submitted for this booking.');
    }
    
    // Check if booking is in a valid state for return
    if (!in_array($booking->status, ['approved', 'needs return'])) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'This booking is not in a valid state for equipment return.');
    }
    
    return view('admin.bookings-return-form', compact('booking'));
}

public function submitReturn(Request $request, Booking $booking)
{
    $request->validate([
        'return_photo' => 'required|image|max:5120',
        'user_condition' => 'required|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ]);
    
    if ($booking->equipmentReturn) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'A return has already been submitted for this booking.');
    }
    
    $now = now();
    
    if ($now->lt($booking->start_datetime)) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'Cannot return before the booking start time.');
    }
    
    $photoPath = $request->file('return_photo')->store('equipment_returns', 'public');
    
    // Create equipment return
    EquipmentReturn::create([
        'booking_id' => $booking->id,
        'return_date' => $now,
        'return_photo_path' => $photoPath,
        'user_condition' => $request->user_condition,
        'condition_status' => 'pending',
        'notes' => $request->notes,
    ]);
    
    // Update booking status
    $booking->update([
        'status' => 'return submitted'
    ]);
    
    return redirect()->route('bookings.show', $booking->id)
        ->with('success', 'Return submitted successfully. Awaiting staff verification.');
}

public function showVerifyForm(Booking $booking)
{
    // Check if user is authorized to verify returns
    if (!in_array(Auth::user()->role, ['admin', 'headmaster'])) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'You are not authorized to verify equipment returns.');
    }
    
    // Check if return exists
    if (!$booking->equipmentReturn) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'No return has been submitted for this booking.');
    }
    
    // Check if return already verified
    if ($booking->equipmentReturn->verified_at) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'This return has already been verified.');
    }
    
    return view('admin.bookings-return-verify', compact('booking'));
}

public function verifyReturn(Request $request, Booking $booking)
{
    // Check if user is authorized to verify returns
    if (!in_array(Auth::user()->role, ['admin', 'headmaster'])) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'You are not authorized to verify equipment returns.');
    }
    
    $request->validate([
        'condition_status' => 'required|string|in:good,damaged,missing',
        'notes' => 'nullable|string|max:1000',
    ]);
    
    $equipmentReturn = $booking->equipmentReturn;
    
    if (!$equipmentReturn) {
        return redirect()->route('bookings.show', $booking->id)
            ->with('error', 'No return has been submitted for this booking.');
    }
    
    // Update equipment return
    $equipmentReturn->update([
        'condition_status' => $request->condition_status,
        'notes' => $request->filled('notes') ? $request->notes : $equipmentReturn->notes,
        'verified_by' => Auth::id(),
        'verified_at' => now(),
    ]);
    
    // Update facility item condition
    $facilityItem = $booking->facilityItem;
    $facilityItem->condition_status = $request->condition_status;
    $facilityItem->save();
    
    // Update booking status
    $booking->update([
        'status' => 'completed'
    ]);
    
    return redirect()->route('bookings.show', $booking->id)
        ->with('success', 'Return verified and equipment condition updated.');
}
}