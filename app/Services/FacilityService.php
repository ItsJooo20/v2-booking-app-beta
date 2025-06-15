<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FacilityService
{
    public function getAllFacilities()
    {
        return Facility::all();
    }

    public function getAllItemsWithFacility()
    {
        return FacilityItem::with('facility.category')->get();
    }

    public function getAllFacilitiesWithItems()
    {
        return Facility::with('items')->get();
    }

    public function getAllFacilitiesWithCategory()
    {
        return Facility::with('category')->get();
    }

    public function getFilteredFacilities(Request $request): LengthAwarePaginator
    {
        $query = Facility::with('category')->withCount('items');
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        return $query->paginate(3);
    }

    public function findById(int $id): Facility
    {
        return Facility::findOrFail($id);
    }

    public function createFacility(array $data): Facility
    {
        return Facility::create($data);
    }

    public function updateFacility(int $id, array $data): Facility
    {
        $facility = $this->findById($id);
        $facility->update($data);
        return $facility;
    }

    public function deleteFacility(int $id): array
    {
        $facility = $this->findById($id);

        if ($facility->items()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete facility with related items.'
            ];
        }

        $facility->delete();

        return [
            'success' => true,
            'message' => 'Facility deleted successfully.'
        ];
    }
}