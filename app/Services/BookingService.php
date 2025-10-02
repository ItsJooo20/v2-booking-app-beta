<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\FacilityItem;
use App\Jobs\SendBookingEmail;
use App\Jobs\SendBookingNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\FirebaseNotificationService;

class BookingService
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $userId = $booking->user_id;
            $facilityName = $booking->facilityItem->facility->name ?? 'Facility';
            $itemCode = $booking->facilityItem->item_code ?? '';
            $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;

            
            // Check if significant changes occurred
            $hasSignificantChanges = false;
            $changeDetails = [];

            if (isset($data['start_datetime']) && $data['start_datetime'] != $booking->start_datetime) {
                $hasSignificantChanges = true;
                $changeDetails[] = 'start time';
            }

            if (isset($data['end_datetime']) && $data['end_datetime'] != $booking->end_datetime) {
                $hasSignificantChanges = true;
                $changeDetails[] = 'end time';
            }

            if (isset($data['facility_item_id']) && $data['facility_item_id'] != $booking->facility_item_id) {
                $hasSignificantChanges = true;
                $changeDetails[] = 'facility';
            }

            $booking->fill($data);
            $booking->save();

            // Send notification only if there are significant changes
            $this->sendBookingNotification(
                $userId,
                'Booking Status Updated 📝',
                "Your booking for {$itemCode} status is now: {$booking->status}",
                [
                    'booking_id' => (string)$booking->id,
                    'status' => $booking->status,
                    'type' => 'booking_status_update'
                ],
                $imageUrl
            );

            return $booking;
        });
    }

    public function cancelBooking(Booking $booking): Booking
    {
        $userId = $booking->user_id;
        $facilityName = $booking->facilityItem->facility->name ?? 'Facility';
        $itemCode = $booking->facilityItem->item_code ?? '';
        $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;

        $booking->status = 'cancelled';
        $booking->save();

        $this->sendBookingNotification(
                $userId,
                'Booking Cancelled!',
                "Your booking for {$itemCode} has been {$booking->status}!",
                [
                    'booking_id' => (string)$booking->id,
                    'status' => 'cancelled',
                    'type' => 'booking_status_update'
                ],
                $imageUrl
            );

        return $booking;
    }

    public function approveBooking(Booking $booking): array
    {
        return DB::transaction(function () use ($booking) {
            $rejectedCount = 0;

            // Get the user ID before status change
            $userId = $booking->user_id;
            $facilityName = $booking->facilityItem->facility->name ?? 'Facility';
            $itemCode = $booking->facilityItem->item_code ?? '';
            $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;

            // Approve the selected booking
            $booking->status = 'approved';
            $booking->save();

            // Send push notification to the user whose booking was approved
            $this->sendBookingNotification(
                $userId,
                'Booking Approved ✅',
                "Your booking for {$itemCode} has been {$booking->status}!",
                [
                    'booking_id' => (string)$booking->id,
                    'status' => 'approved',
                    'type' => 'booking_status_update'
                ],
                $imageUrl
            );

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
                $conflictUserId = $conflict->user_id;
                $conflict->status = 'rejected';
                $conflict->save();

                // Send push notification to users whose bookings were rejected due to conflict
                $this->sendBookingNotification(
                    $conflictUserId,
                    'Booking Rejected ❌',
                    "Your booking for {$itemCode} has been {$booking->status} due to scheduling conflict.",
                    [
                        'booking_id' => (string)$conflict->id,
                        'status' => 'rejected',
                        'type' => 'booking_status_update',
                        'reason' => 'scheduling_conflict'
                    ],
                    $imageUrl
                );
            }

            return [
                'booking' => $booking,
                'rejected_count' => $rejectedCount
            ];
        });
    }

    public function rejectBooking(Booking $booking): Booking
    {
        $userId = $booking->user_id;
        $facilityName = $booking->facilityItem->facility->name ?? 'Facility';
        $itemCode = $booking->facilityItem->item_code ?? '';
        $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;

        $booking->status = 'rejected';
        $booking->save();

        // Send push notification to the user whose booking was rejected
        $this->sendBookingNotification(
            $userId,
            'Booking Rejected ❌',
            "Your booking for {$itemCode} has been {$booking->status}.",
            [
                'booking_id' => (string)$booking->id,
                'status' => 'rejected',
                'type' => 'booking_status_update'
            ],
            $imageUrl
        );

        return $booking;
    }

    public function getAllBookings(): Collection
    {
        return Booking::with(['user', 'facilityItem.facility'])
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function getUpcomingBookings(): LengthAwarePaginator
    {
        return Booking::with(['user', 'facilityItem'])
            ->where('start_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime', 'asc')
            ->paginate();
    }

    public function getUserUpcomingBookings(int $userId): LengthAwarePaginator
    {
        return Booking::with(['user', 'facilityItem'])
            ->where('user_id', $userId)
            ->where('start_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime', 'asc')
            ->paginate();
    }

    public function findById(int $id): Booking
    {
        return Booking::with(['user', 'facilityItem.facility'])->findOrFail($id);
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $booking = new Booking();
            $booking->user_id = $data['user_id']; // bisa juga pakai auth()->id() kalau login
            $booking->facility_item_id = $data['facility_item_id'];
            $booking->start_datetime = $data['start_datetime'];
            $booking->end_datetime = $data['end_datetime'];
            $booking->purpose = $data['purpose'];
            $booking->status = 'pending';
            $booking->save();

            // Pastikan job dikirim setelah transaksi commit
            // DB::afterCommit(function () use ($booking) {
            //     $admins = User::where('role', 'admin')->select('email')->get();
            //     SendBookingEmail::dispatch($booking, $admins);
            //     // (new SendBookingEmail($booking, $admins))->handle();
            // });

            // SendBookingNotificationJob::dispatch($booking);

            $admins = User::where('role', 'superadmin')->get();
        
            foreach ($admins as $index => $admin) {
                SendBookingNotificationJob::dispatch($booking, $admin->id)
                    ->delay(now()->addSeconds($index * 5)); // 5 seconds between each email
            }

            return $booking;
        });
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
            'needs return' => '#FF9800', // Orange color for needs return
            'return submitted' => '#9C27B0', // Purple for return submitted
        ];
        
        return $colors[$status] ?? '#1A73E8';
    }

    /**
     * Update expired bookings including both main bookings and equipment requests
     */
    public function updateExpiredBookings(): array
    {
        $now = Carbon::now();
        $updatedCounts = [
            'main_completed' => 0,
            'main_needs_return' => 0,
            'main_cancelled' => 0,
            'equipment_completed' => 0,
            'equipment_needs_return' => 0,
            'equipment_cancelled' => 0,
            'total' => 0
        ];

        try {
            DB::beginTransaction();

            Log::info('UpdateBookingStatus - Current time: ' . $now->toDateTimeString());

            // 1. Handle expired MAIN bookings
            $mainBookingCounts = $this->updateExpiredMainBookings($now);
            
            // 2. Handle expired EQUIPMENT REQUESTS
            $equipmentRequestCounts = $this->updateExpiredEquipmentRequests($now);

            DB::commit();

            $updatedCounts = [
                'main_completed' => $mainBookingCounts['completed'],
                'main_needs_return' => $mainBookingCounts['needs_return'],
                'main_cancelled' => $mainBookingCounts['cancelled'],
                'equipment_completed' => $equipmentRequestCounts['completed'],
                'equipment_needs_return' => $equipmentRequestCounts['needs_return'],
                'equipment_cancelled' => $equipmentRequestCounts['cancelled'],
                'total' => array_sum($mainBookingCounts) + array_sum($equipmentRequestCounts)
            ];

            Log::info('Booking statuses updated successfully', $updatedCounts);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update expired bookings: ' . $e->getMessage());
            throw $e;
        }

        return $updatedCounts;
    }

    /**
     * Update expired main bookings
     */
    private function updateExpiredMainBookings(Carbon $now): array
    {
        $counts = ['completed' => 0, 'needs_return' => 0, 'cancelled' => 0];

        // Get expired approved main bookings
        $expiredApproved = Booking::where('status', 'approved')
            ->where('end_datetime', '<=', $now)
            ->with([
                'facilityItem.facility.category', 
                'equipmentReturn' => function($query) {
                    $query->whereNotNull('verified_at');
                }
            ])
            ->get();

        foreach ($expiredApproved as $booking) {
            $facility = $booking->facilityItem->facility;
            $category = $facility->category;
            $userId = $booking->user_id;
            $facilityName = $facility->name ?? 'Facility';
            $itemCode = $booking->facilityItem->item_code ?? '';
            $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;
            
            $requiresReturn = $category->requires_return ?? false;

            Log::info("Processing main booking {$booking->id}: requires_return = " . ($requiresReturn ? 'true' : 'false'));

            if ($requiresReturn) {
                $hasVerifiedReturn = $booking->equipmentReturn && 
                                   $booking->equipmentReturn->verified_at !== null;

                if ($hasVerifiedReturn) {
                    $booking->status = 'completed';
                    $booking->updated_at = $now;
                    $booking->save();
                    $counts['completed']++;

                    // Send notification for completed booking
                    $this->sendBookingNotification(
                        $userId,
                        'Booking Completed!',
                        "Your booking for {$itemCode} has been {$booking->status}.",
                        [
                            'booking_id' => (string)$booking->id,
                            'status' => 'completed',
                            'type' => 'booking_status_update',
                            'reason' => 'expired_with_verified_return'
                        ],
                        $imageUrl
                    );

                    Log::info("Main booking {$booking->id} marked as completed (verified return)");
                } else {
                    $booking->status = 'needs return';
                    $booking->updated_at = $now;
                    $booking->save();
                    $counts['needs_return']++;

                    // Send notification for needs return
                    $this->sendBookingNotification(
                        $userId,
                        'Equipment Return Required 🔄',
                        "Your booking for {$itemCode} has expired. Please return the equipment.",
                        [
                            'booking_id' => (string)$booking->id,
                            'status' => 'needs return',
                            'type' => 'booking_status_update',
                            'reason' => 'expired_needs_return'
                        ],
                        $imageUrl
                    );

                    Log::info("Main booking {$booking->id} marked as needs return");
                }
            } else {
                $booking->status = 'completed';
                $booking->updated_at = $now;
                $booking->save();
                $counts['completed']++;

                // Send notification for completed booking
                $this->sendBookingNotification(
                    $userId,
                    'Booking Completed ✅',
                    "Your booking for {$itemCode} has been completed.",
                    [
                        'booking_id' => (string)$booking->id,
                        'status' => 'completed',
                        'type' => 'booking_status_update',
                        'reason' => 'expired_no_return_required'
                    ],
                    $imageUrl
                );

                Log::info("Main booking {$booking->id} marked as completed (no return required)");
            }
        }

        // Handle expired pending main bookings
        $expiredPending = Booking::where('status', 'pending')
            ->where('end_datetime', '<=', $now)
            ->get();

        foreach ($expiredPending as $booking) {
            $userId = $booking->user_id;
            $facilityName = $booking->facilityItem->facility->name ?? 'Facility';
            $itemCode = $booking->facilityItem->item_code ?? '';
            $imageUrl = url('storage/' . $booking->facilityItem->facilityItemImage->image_path)?? null;

            $booking->status = 'cancelled';
            $booking->updated_at = $now;
            $booking->save();
            $counts['cancelled']++;

            // Send notification for cancelled booking
            $this->sendBookingNotification(
                $userId,
                'Booking Cancelled ❌',
                "Your booking for{$itemCode} has been cancelled due to expiration.",
                [
                    'booking_id' => (string)$booking->id,
                    'status' => 'cancelled',
                    'type' => 'booking_status_update',
                    'reason' => 'expired_while_pending'
                ],
                $imageUrl
            );

            Log::info("Main booking {$booking->id} cancelled (expired while pending)");
        }

        return $counts;
    }

    /**
     * Update expired equipment requests
     */
    private function updateExpiredEquipmentRequests(Carbon $now): array
    {
        $counts = ['completed' => 0, 'needs_return' => 0, 'cancelled' => 0];

        // Get expired approved equipment requests
        $expiredApprovedRequests = \App\Models\BookingEquipmentRequest::where('status', 'approved')
            ->whereHas('booking', function($q) use ($now) {
                $q->where('end_datetime', '<=', $now);
            })
            ->with([
                'booking',
                'facilityItem.facility.category',
                'equipmentReturn' => function($query) {
                    $query->whereNotNull('verified_at');
                }
            ])
            ->get();

        foreach ($expiredApprovedRequests as $request) {
            $facility = $request->facilityItem->facility;
            $category = $facility->category;
            
            $requiresReturn = $category->requires_return ?? false;

            Log::info("Processing equipment request {$request->id}: requires_return = " . ($requiresReturn ? 'true' : 'false'));

            if ($requiresReturn) {
                // Check if equipment return exists for this specific equipment request
                $hasVerifiedReturn = $this->hasVerifiedEquipmentReturn($request);

                if ($hasVerifiedReturn) {
                    $request->status = 'completed';
                    $request->updated_at = $now;
                    $request->save();
                    $counts['completed']++;
                    Log::info("Equipment request {$request->id} marked as completed (verified return)");
                } else {
                    $request->status = 'needs return';
                    $request->updated_at = $now;
                    $request->save();
                    $counts['needs_return']++;
                    Log::info("Equipment request {$request->id} marked as needs return");
                }
            } else {
                $request->status = 'completed';
                $request->updated_at = $now;
                $request->save();
                $counts['completed']++;
                Log::info("Equipment request {$request->id} marked as completed (no return required)");
            }
        }

        // Handle expired pending equipment requests
        $expiredPendingRequests = \App\Models\BookingEquipmentRequest::where('status', 'pending')
            ->whereHas('booking', function($q) use ($now) {
                $q->where('end_datetime', '<=', $now);
            })
            ->get();

        foreach ($expiredPendingRequests as $request) {
            $request->status = 'cancelled';
            $request->updated_at = $now;
            $request->save();
            $counts['cancelled']++;
            Log::info("Equipment request {$request->id} cancelled (expired while pending)");
        }

        return $counts;
    }

    /**
     * Check if equipment request has verified return
     * You might need to adjust this based on how you track equipment returns for add-ons
     */
    private function hasVerifiedEquipmentReturn($equipmentRequest): bool
    {
        // Option 1: If equipment returns are linked to the main booking
        // and cover all items including add-ons
        $mainBookingReturn = \App\Models\EquipmentReturn::where('booking_id', $equipmentRequest->booking_id)
            ->whereNotNull('verified_at')
            ->first();

        if ($mainBookingReturn) {
            return true;
        }

        // Option 2: If you have separate equipment returns for each item
        // You might need to create a separate table or modify your current structure
        // For now, let's assume equipment returns are handled at booking level
        
        return false;
    }


    /**
     * Get bookings that are about to expire (within next hour)
     * Useful for sending notifications
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsAboutToExpire()
    {
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

        return Booking::whereIn('status', ['approved', 'pending'])
            ->whereBetween('end_datetime', [$now, $oneHourLater])
            ->get();
    }

    /**
     * Check if a booking is expired
     *
     * @param Booking $booking
     * @return bool
     */
    public function isBookingExpired(Booking $booking): bool
    {
        return Carbon::parse($booking->end_datetime)->isPast();
    }

    public function updateBookingIfExpired(Booking $booking): bool
    {
        if (!$this->isBookingExpired($booking)) {
            return false;
        }

        $newStatus = match($booking->status) {
            'approved' => 'completed',
            'pending' => 'cancelled',
            default => $booking->status
        };

        if ($newStatus !== $booking->status) {
            $booking->update(['status' => $newStatus]);
            return true;
        }

        return false;
    }

    private function sendBookingNotification(int $userId, string $title, string $body, array $data = [], ?string $imageUrl = null)
    {
        try {
            // Get user's device tokens
            $tokens = \App\Models\DeviceToken::where('user_id', $userId)
                                        ->pluck('device_token')
                                        ->toArray();

            if (!empty($tokens)) {
                $result = $this->firebaseService->sendToMultipleTokens(
                    $tokens,
                    $title,
                    $body,
                    $data,
                    $imageUrl // Tambahkan parameter imageUrl
                );

                if ($result['success']) {
                    Log::info("Push notification sent successfully to user {$userId}: {$title}");
                } else {
                    Log::error("Failed to send push notification to user {$userId}: " . $result['message']);
                }
            } else {
                Log::info("No device tokens found for user {$userId}");
            }
        } catch (\Exception $e) {
            Log::error("Error sending push notification to user {$userId}: " . $e->getMessage());
        }
    }
}