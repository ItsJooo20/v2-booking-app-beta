<?php

namespace App\Services;

use App\Models\FacilityCategory;
use Illuminate\Support\Facades\Storage;
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
        if (isset($data['image'])) {
            $data['image_path'] = $this->storeImage($data['image']);
            unset($data['image']);
        }
        
        return FacilityCategory::create($data);
    }

    public function updateCategory(int $id, array $data): FacilityCategory
    {
        $category = $this->findById($id);
        
        if (isset($data['image'])) {
            // Delete old image if exists
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            
            $data['image_path'] = $this->storeImage($data['image']);
            unset($data['image']);
        }
        
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

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return [
            'success' => true,
            'message' => 'Facility category deleted successfully.'
        ];
    }
    
    private function storeImage($image): string
    {
        return $image->store('facility-categories', 'public');
    }
}