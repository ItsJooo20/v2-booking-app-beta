<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FacilityItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    public function getReportData(array $filters): array
    {
        $query = Booking::with(['user', 'facilityItem.facility.category']);
        $this->applyFilters($query, $filters);
        
        $bookings = $query->get();
        $dateText = $this->getDateText($filters);
        
        return [
            'bookings' => $bookings,
            'dateText' => $dateText,
            'filters' => $filters
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        // Apply date filters
        $this->applyDateFilter($query, $filters);
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply user filter
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply facility filter
        if (!empty($filters['facility_id'])) {
            $query->whereHas('facilityItem', function($q) use ($filters) {
                $q->where('facility_id', $filters['facility_id']);
            });
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->whereHas('facilityItem.facility', function($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        // Apply facility item filter
        if (!empty($filters['facility_item_id'])) {
            $query->where('facility_item_id', $filters['facility_item_id']);
        }
    }

    private function applyDateFilter($query, array $filters): void
    {
        switch($filters['date_range_type']) {
            case 'day':
                $date = Carbon::parse($filters['date_day']);
                $query->whereDate('start_datetime', $date);
                break;
                
            case 'range':
                $start = Carbon::parse($filters['date_range_start'])->startOfDay();
                $end = Carbon::parse($filters['date_range_end'])->endOfDay();
                $query->whereBetween('start_datetime', [$start, $end]);
                break;
                
            case 'month':
                $month = Carbon::createFromFormat('Y-m', $filters['date_month']);
                $query->whereBetween('start_datetime', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth()
                ]);
                break;
                
            case 'year':
                $year = Carbon::createFromFormat('Y', $filters['date_year']);
                $query->whereBetween('start_datetime', [
                    $year->copy()->startOfYear(),
                    $year->copy()->endOfYear()
                ]);
                break;
        }
    }

    private function getDateText(array $filters): string
    {
        switch($filters['date_range_type']) {
            case 'day':
                return Carbon::parse($filters['date_day'])->format('F j, Y');
                
            case 'range':
                $start = Carbon::parse($filters['date_range_start']);
                $end = Carbon::parse($filters['date_range_end']);
                return $start->format('M j, Y') . ' to ' . $end->format('M j, Y');
                
            case 'month':
                return Carbon::createFromFormat('Y-m', $filters['date_month'])->format('F Y');
                
            case 'year':
                return Carbon::createFromFormat('Y', $filters['date_year'])->format('Y');
                
            default:
                return '';
        }
    }

    public function generatePdf(array $reportData)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports-pdf', $reportData);
        return $pdf->download('bookings-report-'.now()->format('YmdHis').'.pdf');
    }
}