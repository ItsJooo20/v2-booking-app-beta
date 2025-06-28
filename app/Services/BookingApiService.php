<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\FacilityItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BookingEquipmentRequest;

class BookingApiService
{
    public function getUserBookings(User $user, array $filters = [])
    {
        $query = Booking::with(['facilityItem.facility'])
            ->when(!$user->isAdmin(), function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('start_datetime', 'asc');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function getUpcomingBookings(User $user)
    {
        return Booking::with(['facilityItem'])
            ->where('start_datetime', '>=', Carbon::now())
            ->when(!$user->isAdmin(), function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    /**
     * Get approved events including both main bookings and equipment requests
     */
    public function getApprovedEvents(array $filters)
    {
        // Get main bookings
        $mainBookingsQuery = Booking::with(['facilityItem.facility.category', 'user'])
            ->where('status', 'approved')
            ->where('end_datetime', '>=', now())
            ->orderBy('start_datetime');

        $this->applyEventFilters($mainBookingsQuery, $filters);
        $mainBookings = $mainBookingsQuery->get();

        // Get equipment requests that are approved and have approved parent bookings
        $equipmentRequestsQuery = \App\Models\BookingEquipmentRequest::with([
            'booking.user', 
            'facilityItem.facility.category'
        ])
            ->where('status', 'approved')
            ->whereHas('booking', function($q) {
                $q->where('status', 'approved')
                  ->where('end_datetime', '>=', now());
            });

        // Apply filters for equipment requests
        if (isset($filters['facility_item_id'])) {
            $equipmentRequestsQuery->where('facility_item_id', $filters['facility_item_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $equipmentRequestsQuery->whereHas('booking', function($q) use ($filters) {
                $q->whereBetween('start_datetime', [
                    Carbon::parse($filters['start_date'])->startOfDay(),
                    Carbon::parse($filters['end_date'])->endOfDay()
                ]);
            });
        }

        $equipmentRequests = $equipmentRequestsQuery->get();

        // Combine and format both types
        $allEvents = collect();

        // Add main bookings
        foreach ($mainBookings as $booking) {
            $allEvents->push($this->formatEventData($booking, 'main'));
        }

        // Add equipment requests
        foreach ($equipmentRequests as $request) {
            $allEvents->push($this->formatEquipmentRequestEventData($request));
        }

        return $allEvents->sortBy('start_datetime')->values();
    }

public function getUserBookingHistory(User $user, array $filters)
{
    $history = collect();

    // 1. Get main bookings with all required fields
    $mainBookingsQuery = Booking::where('user_id', $user->id)
        ->with(['facilityItem.facility'])
        ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
        ->when(isset($filters['time_filter']), fn($q) => $this->applyTimeFilter($q, $filters['time_filter']));

    $mainBookings = $mainBookingsQuery->latest('start_datetime')->get();

    // 2. Get equipment requests with parent booking data
    $equipmentRequestsQuery = BookingEquipmentRequest::with([
            'booking', 
            'facilityItem.facility'
        ])
        ->whereHas('booking', fn($q) => $q->where('user_id', $user->id))
        ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
        ->when(isset($filters['time_filter']), fn($q) => $this->applyTimeFilter($q->whereHas('booking'), $filters['time_filter']));

    $equipmentRequests = $equipmentRequestsQuery->get();

    // Format all items with consistent fields
    foreach ($mainBookings as $booking) {
        $history->push($this->formatHistoryItem($booking, 'main'));
    }

    foreach ($equipmentRequests as $request) {
        $history->push($this->formatHistoryItem($request->booking, 'equipment_request', $request));
    }

    return $history->sortByDesc('start_datetime_sort')->values();
}

private function formatHistoryItem($booking, $type, $request = null)
{
    $baseData = [
        'id' => $type === 'main' ? $booking->id : $request->id,
        'booking_id' => $booking->id, // Always the parent booking ID
        'type' => $type,
        'facility_name' => $type === 'main' 
            ? $booking->facilityItem->facility->name 
            : $request->facilityItem->facility->name,
        'item_code' => $type === 'main' 
            ? $booking->facilityItem->item_code 
            : $request->facilityItem->item_code,
        'start_datetime' => $booking->start_datetime,
        'end_datetime' => $booking->end_datetime,
        'purpose' => $booking->purpose,
        'status' => $type === 'main' ? $booking->status : $request->status,
        'start_datetime_sort' => $booking->start_datetime,
        'created_at' => $type === 'main' ? $booking->created_at : $request->created_at,
        'updated_at' => $type === 'main' ? $booking->updated_at : $request->updated_at,
    ];

    // Add request-specific fields if needed
    if ($type === 'equipment_request') {
        $baseData['request_id'] = $request->id;
    }

    return $baseData;
}

private function applyTimeFilter($query, $timeFilter)
{
    $today = Carbon::today();
    
    return match ($timeFilter) {
        'today' => $query->whereDate('start_datetime', $today),
        'week' => $query->whereBetween('start_datetime', [
            $today->startOfWeek(), 
            $today->endOfWeek()
        ]),
        'month' => $query->whereBetween('start_datetime', [
            $today->startOfMonth(), 
            $today->endOfMonth()
        ]),
        default => $query
    };
}

    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $booking = new Booking();
            $booking->user_id = $user->id;
            $booking->fill($data);
            $booking->status = 'pending';
            $booking->save();
            
            return $booking;
        });
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->fill($data);
            $booking->save();
            return $booking;
        });
    }

    public function approveBooking(Booking $booking): array
    {
        return DB::transaction(function () use ($booking) {
            $rejectedCount = 0;

            $booking->status = 'approved';
            $booking->save();

            $conflictingBookings = $this->getConflictingBookings($booking);
            $rejectedCount = $conflictingBookings->count();

            foreach ($conflictingBookings as $conflict) {
                $conflict->status = 'rejected';
                $conflict->save();
            }

            return [
                'booking' => $booking,
                'rejected_count' => $rejectedCount
            ];
        });
    }

    public function checkAvailability(int $facilityItemId, string $start, string $end, ?int $excludeId = null): bool
    {
        // 1. Check direct bookings (as main)
        $main = Booking::where('facility_item_id', $facilityItemId)
            ->whereIn('status', ['approved', 'completed', 'pending'])
            ->where(function($q) use ($start, $end) {
                $this->addOverlapConditions($q, $start, $end);
            });

        if ($excludeId) {
            $main->where('id', '!=', $excludeId);
        }

        if ($main->exists()) {
            return false;
        }

        $addon = \App\Models\BookingEquipmentRequest::where('facility_item_id', $facilityItemId)
            ->where('status', '!=', 'rejected')
            ->whereHas('booking', function($q) use ($start, $end, $excludeId) {
                $q->whereIn('status', ['approved', 'completed', 'pending'])
                ->where(function($qq) use ($start, $end) {
                    $this->addOverlapConditions($qq, $start, $end);
                });
                if ($excludeId) $q->where('id', '!=', $excludeId);
            })
            ->exists();

        return !$addon;
    }

    public function checkEquipmentAvailability(int $equipmentId, string $start, string $end): bool
    {
        $main = Booking::where('facility_item_id', $equipmentId)
            ->whereIn('status', ['approved', 'completed', 'pending'])
            ->where(function($q) use ($start, $end) {
                $this->addOverlapConditions($q, $start, $end);
            })
            ->exists();

        if ($main) return false;

        $addon = \App\Models\BookingEquipmentRequest::where('facility_item_id', $equipmentId)
            ->whereHas('booking', function($q) use ($start, $end) {
                $q->whereIn('status', ['approved', 'completed', 'pending'])
                    ->where(function($qq) use ($start, $end) {
                        $this->addOverlapConditions($qq, $start, $end);
                    });
            })
            ->exists();

        return !$addon;
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    public function validateAddOns($facilityItemId, $equipmentIds = [])
    {
        $mainItem = FacilityItem::with('facility')->find($facilityItemId);
        $mainFacility = $mainItem->facility;

        if (!$mainFacility->can_have_addon && !empty($equipmentIds)) {
            throw new \Exception('This facility cannot have add-ons.');
        }

        foreach ($equipmentIds as $equipId) {
            $equip = FacilityItem::with('facility')->find($equipId);
            if (!$equip || !$equip->facility->can_be_addon) {
                throw new \Exception('Selected add-on is not allowed.');
            }
        }
    }

    private function applyEventFilters($query, array $filters): void
    {
        if (isset($filters['facility_item_id'])) {
            $query->where('facility_item_id', $filters['facility_item_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('start_datetime', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay()
            ]);
        }
    }

    private function applyHistoryFilters($query, array $filters)
{
    if (isset($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    if (isset($filters['time_filter'])) {
        $today = Carbon::today();
        
        switch ($filters['time_filter']) {
            case 'today':
                $query->whereDate('start_datetime', $today);
                break;
            case 'week':
                $query->whereBetween('start_datetime', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('start_datetime', [
                    $today->copy()->startOfMonth(),
                    $today->copy()->endOfMonth()
                ]);
                break;
        }
    }
}

private function formatHistoryData($booking, $type = 'main')
{
    return [
        'id' => $booking->id,
        'type' => $type,
        'facility_name' => $booking->facilityItem->facility->name ?? 'N/A',
        'item_code' => $booking->facilityItem->item_code ?? 'N/A',
        'start_datetime' => $booking->start_datetime,
        'end_datetime' => $booking->end_datetime,
        'purpose' => $booking->purpose,
        'status' => $booking->status,
        'start_datetime_sort' => $booking->start_datetime, // For sorting
        'created_at' => $booking->created_at,
        'updated_at' => $booking->updated_at,
    ];
}

private function formatEquipmentRequestHistoryData($request)
{
    return [
        'id' => $request->id,
        'type' => 'equipment_request',
        'booking_id' => $request->booking_id,
        'facility_name' => $request->facilityItem->facility->name ?? 'N/A',
        'item_code' => $request->facilityItem->item_code ?? 'N/A',
        'start_datetime' => $request->booking->start_datetime,
        'end_datetime' => $request->booking->end_datetime,
        'purpose' => $request->booking->purpose,
        'status' => $request->status,
        'start_datetime_sort' => $request->booking->start_datetime, // For sorting
        'created_at' => $request->created_at,
        'updated_at' => $request->updated_at,
    ];
}

    private function formatEventData($event, $type = 'main'): array
    {
        return [
            'id' => $event->id,
            'type' => $type,
            'facility_item_id' => $event->facility_item_id,
            'start_datetime' => $event->start_datetime->format('Y-m-d H:i:s'),
            'end_datetime' => $event->end_datetime->format('Y-m-d H:i:s'),
            'start_date' => $event->start_datetime->format('D, M j, Y'),
            'start_time' => $event->start_datetime->format('g:i A'),
            'end_time' => $event->end_datetime->format('g:i A'),
            'duration' => $event->start_datetime->diffInHours($event->end_datetime) . ' hours',
            'purpose' => $event->purpose,
            'status' => $event->status,
            'facility_item' => $event->facilityItem,
            'user' => $event->user
        ];
    }

    private function formatEquipmentRequestEventData($request): array
    {
        $booking = $request->booking;
        return [
            'id' => $request->id,
            'type' => 'equipment_request',
            'booking_id' => $booking->id,
            'facility_item_id' => $request->facility_item_id,
            'start_datetime' => $booking->start_datetime->format('Y-m-d H:i:s'),
            'end_datetime' => $booking->end_datetime->format('Y-m-d H:i:s'),
            'start_date' => $booking->start_datetime->format('D, M j, Y'),
            'start_time' => $booking->start_datetime->format('g:i A'),
            'end_time' => $booking->end_datetime->format('g:i A'),
            'duration' => $booking->start_datetime->diffInHours($booking->end_datetime) . ' hours',
            'purpose' => $booking->purpose,
            'status' => $request->status,
            'facility_item' => $request->facilityItem,
            'user' => $booking->user
        ];
    }



    private function getConflictingBookings(Booking $booking)
    {
        return Booking::where('facility_item_id', $booking->facility_item_id)
            ->where('id', '!=', $booking->id)
            ->where('status', 'pending')
            ->where(function($query) use ($booking) {
                $this->addOverlapConditions($query, $booking->start_datetime, $booking->end_datetime);
            })
            ->get();
    }

    private function addOverlapConditions($query, $start, $end)
    {
        $query->where(function($q) use ($start, $end) {
            $q->where('start_datetime', '>=', $start)
              ->where('start_datetime', '<', $end);
        })->orWhere(function($q) use ($start, $end) {
            $q->where('end_datetime', '>', $start)
              ->where('end_datetime', '<=', $end);
        })->orWhere(function($q) use ($start, $end) {
            $q->where('start_datetime', '<=', $start)
              ->where('end_datetime', '>=', $end);
        });
    }
}