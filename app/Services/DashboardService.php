<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\FacilityItem;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData(User $user)
    {
        $upcomingBookings = $this->getUpcomingBookings($user);
        $calendarBookings = $this->getCalendarBookings($user);
        $stats = $this->getStats($user);

        return compact('upcomingBookings', 'calendarBookings', 'stats');
    }

    private function getUpcomingBookings(User $user)
    {
        return Booking::with(['facilityItem', 'user'])
            ->when(!in_array($user->role, ['admin', 'headmaster']), function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->where('status', 'approved')
            ->where('end_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime')
            ->take(3)
            ->get();
    }

    private function getCalendarBookings(User $user)
    {
        return Booking::with(['facilityItem'])
            ->when(!in_array($user->role, ['admin', 'headmaster']), function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->whereIn('status', ['approved'])
            ->where('end_datetime', '>=', Carbon::now()->subDays(30))
            ->where('start_datetime', '<=', Carbon::now()->addDays(30))
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->facilityItem->item_code . ' - ' . $booking->purpose,
                    'start' => $booking->start_datetime->format('Y-m-d\TH:i:s'),
                    'end' => $booking->end_datetime->format('Y-m-d\TH:i:s'),
                    'color' => $this->getStatusColor($booking->status),
                    'url' => route('admin.dashboard', $booking->id),
                ];
            });
    }

    private function getStats(User $user)
    {
        if (!in_array($user->role, ['admin', 'headmaster'])) {
            return [];
        }

        return [
            'total_facilities' => FacilityItem::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_bookings' => Booking::where('status', 'approved')
                ->where('start_datetime', '>=', Carbon::now())
                ->where('end_datetime', '>=', Carbon::now())
                ->count(),
        ];
    }

    private function getStatusColor($status)
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