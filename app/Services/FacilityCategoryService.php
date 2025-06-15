<?php

namespace App\Services;

use App\Models\FacilityCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class FacilityCategoryService
{
    public function getAllCategories()
    {
        return FacilityCategory::all();
    }
    
    public function getPaginatedCategories(int $perPage): LengthAwarePaginator
    {
        return FacilityCategory::withCount('facilities')->paginate($perPage);
    }

    public function findById(int $id): FacilityCategory
    {
        return FacilityCategory::findOrFail($id);
    }

    public function createCategory(array $data): FacilityCategory
    {
        return FacilityCategory::create($data);
    }

    public function updateCategory(int $id, array $data): FacilityCategory
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory(int $id): array
    {
        $category = $this->findById($id);

        if ($category->facilities()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete category with related facilities.'
            ];
        }

        $category->delete();

        return [
            'success' => true,
            'message' => 'Facility category deleted successfully.'
        ];
    }
}