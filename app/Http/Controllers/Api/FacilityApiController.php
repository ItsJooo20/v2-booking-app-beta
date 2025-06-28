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
        return $this->successResponse($items);
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