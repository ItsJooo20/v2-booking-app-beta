<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Models\FacilityItem;
use App\Services\FacilityApiService;
use Illuminate\Http\Request;

class FacilityApiController extends Controller
{
    protected $facilityService;

    public function __construct(FacilityApiService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function categories(Request $request)
    {
        $categories = $this->facilityService->getCategories();
        return $this->successResponse($categories);
    }

    public function facilities(Request $request)
    {
        $categoryId = $request->has('category_id') ? $request->category_id : null;
        $facilities = $this->facilityService->getFacilities($categoryId);
        return $this->successResponse($facilities);
    }

    public function items(Request $request)
    {
        $facilityId = $request->has('facility_id') ? $request->facility_id : null;
        $status = $request->has('status') ? $request->status : null;
        
        $items = $this->facilityService->getItems($facilityId, $status);
        
        $transformedItems = $items->map(function($item) {
            $primaryImage = $item->getPrimaryImage();
            $primaryImageUrl = $primaryImage ? url('storage/' . $primaryImage->image_path) : null;
            
            $itemData = $item->toArray();
            $itemData['primary_image_url'] = $primaryImageUrl;
            
            if ($item->images && $item->images->count() > 0) {
                $itemData['images'] = $item->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                        'is_primary' => $image->is_primary,
                        'image_url' => url('storage/' . $image->image_path)
                    ];
                });
            } else {
                $itemData['images'] = [];
            }
            
            return $itemData;
        });
        
        return $this->successResponse($transformedItems);
    }

    public function itemDetails(FacilityItem $item)
    {
        $item = $this->facilityService->getItemDetails($item);
        return $this->successResponse($item);
    }

    public function checkAvailability(CheckAvailabilityRequest $request, FacilityItem $item)
    {
        $result = $this->facilityService->checkAvailability($item, $request->validated());
        return $this->successResponse($result);
    }

    private function successResponse($data, string $message = '')
    {
        return response()->json([
            'status' => true,
            // 'message' => $message,
            'data' => $data
        ]);
    }
}