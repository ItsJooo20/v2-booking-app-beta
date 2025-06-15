<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FacilityItemService
{
    public function getFilteredItems(Request $request): LengthAwarePaginator
    {
        $query = FacilityItem::with('facility.category');
        
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        
        return $query->paginate(3);
    }

    public function findById(int $id): FacilityItem
    {
        return FacilityItem::findOrFail($id);
    }

    public function createItem(array $data): FacilityItem
    {
        return FacilityItem::create($data);
    }

    public function updateItem(int $id, array $data): array
    {
        $facilityItem = $this->findById($id);
        $oldFacilityId = $facilityItem->facility_id;
        
        $facilityItem->update($data);
        
        return [
            'facilityItem' => $facilityItem,
            'oldFacilityId' => $oldFacilityId
        ];
    }

    public function deleteItem(int $id): array
    {
        $facilityItem = $this->findById($id);

        if ($facilityItem->bookings()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete facility item with related bookings.',
                'facilityId' => null
            ];
        }

        $facilityId = $facilityItem->facility_id;
        $facilityItem->delete();

        return [
            'success' => true,
            'message' => 'Facility item deleted successfully.',
            'facilityId' => $facilityId
        ];
    }

    public function updateFacilityCounts(int $facilityId): void
    {
        $facility = Facility::findOrFail($facilityId);
        $totalItems = $facility->items()->count();
        $availableItems = $facility->items()->count();
        
        $facility->update([
            'total_items' => $totalItems,
            'available_items' => $availableItems
        ]);
    }
}