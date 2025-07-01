<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityItem;
use App\Models\FacilityCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class FacilityApiService
{
    public function getCategories()
    {
        // return Cache::remember('facility_categories', now()->addDay(), function() {
        //     return FacilityCategory::withCount('facilities')
        //         ->orderBy('name')
        //         ->get();
        // });
            return FacilityCategory::withCount('facilities')
                ->orderBy('name')
                ->get();
    }

    public function getFacilities(?int $categoryId = null): Collection
    {
        $query = Facility::with(['category'])
            ->withCount('items')
            ->orderBy('name');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    public function getItems(?int $facilityId = null, ?string $status = null): Collection
    {
        $query = FacilityItem::with(['facility.category', 'images'])
            ->orderBy('item_code');

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getItemDetails(FacilityItem $item): FacilityItem
    {
        $item->load(['facility.category', 'images'])->get();

        return $item;
    }

    public function checkAvailability(FacilityItem $item, array $data): array
    {
        $query = Booking::where('facility_item_id', $item->id)
            ->whereIn('status', ['approved', 'completed'])
            ->where(function($query) use ($data) {
                $this->addOverlapConditions($query, $data['start_datetime'], $data['end_datetime']);
            });

        if (!empty($data['exclude_booking_id'])) {
            $query->where('id', '!=', $data['exclude_booking_id']);
        }

        $conflicts = $query->get();
        $isAvailable = $conflicts->isEmpty();

        return [
            'available' => $isAvailable,
            'item' => $item,
            'conflicts' => $isAvailable ? [] : $conflicts
        ];
    }

    private function addOverlapConditions($query, string $start, string $end): void
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