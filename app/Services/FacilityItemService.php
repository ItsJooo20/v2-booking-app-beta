<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityItem;
use App\Models\FacilityItemImage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FacilityItemService
{
    public function getFilteredItems(Request $request): LengthAwarePaginator
    {
        $query = FacilityItem::with(['facility.category', 'images']);
        
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        
        return $query->paginate(6);
    }

    public function findById(int $id): FacilityItem
    {
        return FacilityItem::with('images')->findOrFail($id);
    }

    public function createItem(array $data): FacilityItem
    {
        DB::beginTransaction();
        
        try {
            // Remove images from data to create item first
            $images = $data['images'] ?? null;
            $primaryImageIndex = $data['primary_image'] ?? 0;
            
            unset($data['images']);
            unset($data['primary_image']);
            
            // Create the facility item
            $facilityItem = FacilityItem::create($data);
            
            // Process images if any
            if ($images) {
                $this->saveItemImages($facilityItem, $images, $primaryImageIndex);
            }
            
            DB::commit();
            return $facilityItem;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateItem(int $id, array $data): array
    {
        DB::beginTransaction();
        
        try {
            $facilityItem = $this->findById($id);
            $oldFacilityId = $facilityItem->facility_id;
            
            // Extract image-related data
            $newImages = $data['images'] ?? null;
            $primaryImageId = $data['primary_image'] ?? null;
            $deleteImages = $data['delete_images'] ?? [];
            
            unset($data['images']);
            unset($data['primary_image']);
            unset($data['delete_images']);
            
            // Update the facility item
            $facilityItem->update($data);
            
            // Delete images if specified
            if (!empty($deleteImages)) {
                foreach ($deleteImages as $imageId) {
                    $image = FacilityItemImage::find($imageId);
                    if ($image && $image->facility_item_id == $id) {
                        // Delete the file from storage
                        Storage::disk('public')->delete($image->image_path);
                        // Delete the record
                        $image->delete();
                    }
                }
            }
            
            // Add new images if any
            if ($newImages) {
                $this->saveItemImages($facilityItem, $newImages);
            }
            
            // Update primary image if specified
            if ($primaryImageId !== null) {
                // First, set all images as non-primary
                FacilityItemImage::where('facility_item_id', $id)
                    ->update(['is_primary' => false]);
                
                // Then set the selected image as primary
                FacilityItemImage::where('id', $primaryImageId)
                    ->where('facility_item_id', $id)
                    ->update(['is_primary' => true]);
            }
            
            DB::commit();
            
            return [
                'facilityItem' => $facilityItem->fresh(['images']),
                'oldFacilityId' => $oldFacilityId
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
        
        // Delete all associated images
        foreach ($facilityItem->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
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
        
        // Don't filter by status since that column doesn't exist
        $availableItems = $totalItems; // Or set to the same value as total items
        
        $facility->update([
            'total_items' => $totalItems,
            'available_items' => $availableItems
        ]);
    }
    
    /**
     * Save images for a facility item
     */
    private function saveItemImages(FacilityItem $facilityItem, array $images, ?int $primaryIndex = null): void
    {
        $currentCount = $facilityItem->images()->count();
        
        foreach ($images as $index => $image) {
            $path = $image->store('facility-items', 'public');
            
            $facilityItem->images()->create([
                'image_path' => $path,
                'is_primary' => ($primaryIndex !== null && $index == $primaryIndex) || ($primaryIndex === null && $currentCount == 0 && $index == 0),
                'display_order' => $currentCount + $index
            ]);
        }
    }
}