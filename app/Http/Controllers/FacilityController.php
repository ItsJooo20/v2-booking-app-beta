<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacilityService;
use App\Services\FacilityCategoryService;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;

class FacilityController extends Controller
{
    protected $facilityService;
    protected $facilityCategoryService;

    public function __construct(
        FacilityService $facilityService,
        FacilityCategoryService $facilityCategoryService
    ) {
        $this->facilityService = $facilityService;
        $this->facilityCategoryService = $facilityCategoryService;
    }

    public function index(Request $request)
    {
        $facilities = $this->facilityService->getFilteredFacilities($request);
        $categories = $this->facilityCategoryService->getAllCategories();
        $highlightFacility = $request->highlight;
        
        return view('admin.facilities-index', compact('facilities', 'categories', 'highlightFacility'));
    }

    public function create()
    {
        $categories = $this->facilityCategoryService->getAllCategories();
        return view('admin.facilities-create', compact('categories'));
    }

    public function store(StoreFacilityRequest $request)
    {
        $this->facilityService->createFacility($request->validated());

        return redirect()->route('facilities.index')
            ->with('success', 'Facility created successfully.');
    }

    public function edit($id)
    {
        $facility = $this->facilityService->findById($id);
        $categories = $this->facilityCategoryService->getAllCategories();
        
        return view('admin.facilities-edit', compact('facility', 'categories'));
    }

    public function update(UpdateFacilityRequest $request, $id)
    {
        $this->facilityService->updateFacility($id, $request->validated());

        return redirect()->route('facilities.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function destroy($id)
    {
        $result = $this->facilityService->deleteFacility($id);

        if (!$result['success']) {
            return redirect()->route('facilities.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('facilities.index')
            ->with('success', $result['message']);
    }
}