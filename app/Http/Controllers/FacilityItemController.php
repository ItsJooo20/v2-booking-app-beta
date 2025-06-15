<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacilityService;
use App\Services\FacilityItemService;
use App\Http\Requests\StoreFacilityItemRequest;
use App\Http\Requests\UpdateFacilityItemRequest;

class FacilityItemController extends Controller
{
    protected $facilityItemService;
    protected $facilityService;

    public function __construct(
        FacilityItemService $facilityItemService,
        FacilityService $facilityService
    ) {
        $this->facilityItemService = $facilityItemService;
        $this->facilityService = $facilityService;
    }

    public function index(Request $request)
    {
        $facilityItems = $this->facilityItemService->getFilteredItems($request);
        $facilities = $this->facilityService->getAllFacilities();
        
        return view('admin.facility-items-index', compact('facilityItems', 'facilities'));
    }

    public function create()
    {
        $facilities = $this->facilityService->getAllFacilitiesWithCategory();
        return view('admin.facility-items-create', compact('facilities'));
    }

    public function store(StoreFacilityItemRequest $request)
    {
        $facilityItem = $this->facilityItemService->createItem($request->validated());
        $this->facilityItemService->updateFacilityCounts($facilityItem->facility_id);

        return redirect()->route('facility-items.index')
            ->with('success', 'Facility item created successfully.');
    }

    public function edit($id)
    {
        $facilityItem = $this->facilityItemService->findById($id);
        $facilities = $this->facilityService->getAllFacilitiesWithCategory();
        
        return view('admin.facility-items-edit', compact('facilityItem', 'facilities'));
    }

    public function update(UpdateFacilityItemRequest $request, $id)
    {
        $result = $this->facilityItemService->updateItem($id, $request->validated());

        if ($result['oldFacilityId'] != $result['facilityItem']->facility_id) {
            $this->facilityItemService->updateFacilityCounts($result['oldFacilityId']);
        }
        $this->facilityItemService->updateFacilityCounts($result['facilityItem']->facility_id);

        return redirect()->route('facility-items.index')
            ->with('success', 'Facility item updated successfully.');
    }

    public function destroy($id)
    {
        $result = $this->facilityItemService->deleteItem($id);

        if (!$result['success']) {
            return redirect()->route('facility-items.index')
                ->with('error', $result['message']);
        }

        $this->facilityItemService->updateFacilityCounts($result['facilityId']);

        return redirect()->route('facility-items.index')
            ->with('success', $result['message']);
    }
}