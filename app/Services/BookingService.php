<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FacilityItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function getAllBookings(): Collection
    {
        return Booking::with(['user', 'facilityItem.facility'])
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function getUpcomingBookings(int $perPage = 3): LengthAwarePaginator
    {
        return Booking::with(['user', 'facilityItem'])
            ->where('start_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime', 'asc')
            ->paginate($perPage);
    }

    public function getUserUpcomingBookings(int $userId, int $perPage = 3): LengthAwarePaginator
    {
        return Booking::with(['user', 'facilityItem'])
            ->where('user_id', $userId)
            ->where('start_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime', 'asc')
            ->paginate($perPage);
    }

    public function findById(int $id): Booking
    {
        return Booking::with(['user', 'facilityItem.facility'])->findOrFail($id);
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $booking = new Booking();
            $booking->user_id = $data['user_id']; // Changed from auth()->id()
            $booking->facility_item_id = $data['facility_item_id'];
            $booking->start_datetime = $data['start_datetime'];
            $booking->end_datetime = $data['end_datetime'];
            $booking->purpose = $data['purpose'];
            $booking->status = 'pending';
            $booking->save();
            
            return $booking;
        });
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->fill($data);
            
            // If not admin updating, reset status to pending
            // if (!in_array(auth()->user()->role, ['admin', 'headmaster'])) {
            //     $booking->status = 'pending';
            // }
            
            $booking->save();
            return $booking;
        });
    }

    public function cancelBooking(Booking $booking): Booking
    {
        $booking->status = 'cancelled';
        $booking->save();
        return $booking;
    }

    public function approveBooking(Booking $booking): array
    {
        return DB::transaction(function () use ($booking) {
            $rejectedCount = 0;

            // Approve the selected booking
            $booking->status = 'approved';
            $booking->save();

            // Find and reject all conflicting pending bookings
            $conflictingBookings = Booking::where('facility_item_id', $booking->facility_item_id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'pending')
                ->where(function($query) use ($booking) {
                    $this->addOverlapConditions($query, $booking);
                })
                ->get();

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

    public function rejectBooking(Booking $booking): Booking
    {
        $booking->status = 'rejected';
        $booking->save();
        return $booking;
    }

    public function checkAvailability(int $facilityItemId, string $start, string $end, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('facility_item_id', $facilityItemId)
            ->whereIn('status', ['approved', 'completed'])
            ->where(function($query) use ($start, $end) {
                $this->addOverlapConditions($query, null, $start, $end);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    public function formatForCalendar(Collection $bookings): array
    {
        return $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->facilityItem->item_code . ' - ' . $booking->purpose,
                'start' => $booking->start_datetime->format('Y-m-d\TH:i:s'),
                'end' => $booking->end_datetime->format('Y-m-d\TH:i:s'),
                'color' => $this->getStatusColor($booking->status),
                'url' => route('bookings.show', $booking->id),
            ];
        })->toArray();
    }

    private function addOverlapConditions($query, ?Booking $booking = null, ?string $start = null, ?string $end = null)
    {
        $start = $booking ? $booking->start_datetime : $start;
        $end = $booking ? $booking->end_datetime : $end;

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

    private function getStatusColor(string $status): string
    {
        $colors = [
            'pending' => '#FBBC05',
            'approved' => '#34A853',
            'rejected' => '#EA4335',
            'completed' => '#1A73E8',
            'cancelled' => '#5F6368',
        ];
        
        return $colors[$status] ?? '#1A73E8';
    }
}