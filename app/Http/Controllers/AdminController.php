<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FacilityItem;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $data = $this->dashboardService->getDashboardData($user);
        
        return view('admin.dashboard', $data);
    }
}