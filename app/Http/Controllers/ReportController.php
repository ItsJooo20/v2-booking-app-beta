<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\FacilityItem;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $data = [
            'users' => User::all(),
            'facilities' => Facility::with('category')->get(),
            'categories' => FacilityCategory::all(),
            'facilityItems' => FacilityItem::all(),
            'dateRanges' => [
                'day' => now()->format('Y-m-d'),
                'range' => [
                    'start' => now()->startOfWeek()->format('Y-m-d'),
                    'end' => now()->endOfWeek()->format('Y-m-d')
                ],
                'month' => now()->format('Y-m'),
                'year' => now()->format('Y')
            ]
        ];

        return view('admin.reports-index', $data);
    }

    public function generate(GenerateReportRequest $request)
    {
        $reportData = $this->reportService->getReportData($request->validated());
        return view('admin.reports-result', $reportData);
    }

    public function downloadPdf(GenerateReportRequest $request)
    {
        $reportData = $this->reportService->getReportData($request->validated());
        return $this->reportService->generatePdf($reportData);
    }
}