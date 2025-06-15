<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacilityCategoryService;
use App\Http\Requests\StoreFacilityCategoryRequest;
use App\Http\Requests\UpdateFacilityCategoryRequest;

class FacilityCategoryController extends Controller
{
    protected $facilityCategoryService;

    public function __construct(FacilityCategoryService $facilityCategoryService)
    {
        $this->facilityCategoryService = $facilityCategoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->facilityCategoryService->getPaginatedCategories(6);
        $highlightCategory = $request->highlight;
        
        return view('admin.facility-category-index', compact('categories', 'highlightCategory'));
    }

    public function create()
    {
        return view('admin.facility-category-create');
    }

    public function store(StoreFacilityCategoryRequest $request)
    {
        $this->facilityCategoryService->createCategory($request->validated());

        return redirect()->route('facility-categories.index')
            ->with('success', 'Facility category created successfully.');
    }

    public function edit($id)
    {
        $category = $this->facilityCategoryService->findById($id);
        return view('admin.facility-category-edit', compact('category'));
    }

    public function update(UpdateFacilityCategoryRequest $request, $id)
    {
        $this->facilityCategoryService->updateCategory($id, $request->validated());

        return redirect()->route('facility-categories.index')
            ->with('success', 'Facility category updated successfully.');
    }

    public function destroy($id)
    {
        $result = $this->facilityCategoryService->deleteCategory($id);

        if (!$result['success']) {
            return redirect()->route('facility-categories.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('facility-categories.index')
            ->with('success', $result['message']);
    }
}