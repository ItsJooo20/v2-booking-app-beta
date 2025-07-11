@extends('layout.navbar-calendar')

@section('title', 'Bookings')

@section('styles')
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Calendar Styling */
    .fc .fc-button-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .fc .fc-button-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    .fc-event {
        border-radius: 6px;
        padding: 2px 4px;
        border: none;
    }
    #bookingCalendar {
        min-height: 600px;
    }
    
    /* Badge styling for statuses */
    .badge.bg-orange {
        background-color: #FF9800 !important;
    }
    
    .badge.bg-purple {
        background-color: #9C27B0 !important;
        color: white !important;
    }
    
    /* Fix for badge visibility */
    .badge {
        display: inline-block !important;
        padding: 0.35em 0.65em !important;
        font-size: 0.75em !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        text-align: center !important;
        white-space: nowrap !important;
        vertical-align: baseline !important;
        border-radius: 0.25rem !important;
    }
    
    /* Action indicator */
    .action-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #dc3545;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    /* Card styling */
    .booking-card {
        transition: transform 0.2s;
    }
    .booking-card:hover {
        transform: translateY(-5px);
    }
    
    /* Tab styling */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
    }
    
    .nav-tabs .nav-link {
        margin-bottom: -1px;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link:hover, 
    .nav-tabs .nav-link:focus {
        border-color: #e9ecef #e9ecef #dee2e6;
    }
    
    .nav-tabs .nav-link.active,
    .nav-tabs .nav-item.show .nav-link {
        color: var(--primary-color);
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        border-bottom: 3px solid var(--primary-color);
    }
    
    .status-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        font-size: 11px;
        font-weight: 600;
        background-color: rgba(0,0,0,0.1);
        color: inherit;
        border-radius: 10px;
        margin-left: 4px;
    }
    
    /* Custom Pagination Styling */
    .custom-pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        min-width: 36px;
        padding: 0 10px;
        font-size: 0.875rem;
    }
    
    .custom-pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Facility Bookings</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Booking
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif
    
    @php
        // Count bookings by status
        $pendingCount = $upcomingBookings->where('status', 'pending')->count();
        
        // Try to get return submitted bookings 
        $returnSubmitted = \App\Models\Booking::where('status', 'return submitted')->get();
        $returnCount = $returnSubmitted->count();
        
        // Combine for total action count
        $actionCount = $pendingCount + $returnCount;
    @endphp

    <!-- Bootstrap Tabs -->
    <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="action-tab" data-bs-toggle="tab" data-bs-target="#action-pane" type="button" role="tab" aria-controls="action-pane" aria-selected="true">
                Action Required
                <span class="status-count">{{ $actionCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="false">
                Pending
                <span class="status-count">{{ $pendingCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="return-tab" data-bs-toggle="tab" data-bs-target="#return-pane" type="button" role="tab" aria-controls="return-pane" aria-selected="false">
                Return Submitted
                <span class="status-count">{{ $returnCount }}</span>
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="bookingTabsContent">        
        <!-- Action Required Tab -->
        <div class="tab-pane fade show active" id="action-pane" role="tabpanel" aria-labelledby="action-tab" tabindex="0">
            <div class="row">
                @php
                    $actionBookings = $upcomingBookings->where('status', 'pending')
                        ->merge($returnSubmitted);
                @endphp
                
                @forelse($actionBookings as $booking)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm booking-card">
                        <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="action-dot" title="Action required"></span>
                                {{ $booking->facilityItem->item_code ?? 'Item' }}
                            </h5>
                            <span class="badge 
                                @if($booking->status === 'pending') bg-warning text-dark
                                @elseif($booking->status === 'return submitted') bg-purple
                                @endif" style="display: inline-block !important;">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </div>
                        
                        <div class="card-body pt-2">
                            <div class="mb-2">
                                <i class="bi bi-clock text-muted me-1"></i>
                                <small class="text-muted">
                                    @if(isset($booking->start_datetime))
                                        @if($booking->start_datetime instanceof \Carbon\Carbon)
                                            {{ $booking->start_datetime->format('M d, Y g:i A') }} - 
                                            {{ $booking->end_datetime->format('M d, Y g:i A') }}
                                        @else
                                            {{ date('M d, Y g:i A', strtotime($booking->start_datetime)) }} - 
                                            {{ date('M d, Y g:i A', strtotime($booking->end_datetime)) }}
                                        @endif
                                    @else
                                        Time not available
                                    @endif
                                </small>
                            </div>
                            
                            <p class="card-text mb-2">
                                {{ isset($booking->purpose) ? \Illuminate\Support\Str::limit($booking->purpose, 100) : 'No purpose provided' }}
                            </p>
                            
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-person me-1"></i>
                                {{ isset($booking->user) && isset($booking->user->name) ? $booking->user->name : 'User' }}
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View Details & Take Action
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No pending actions required</h5>
                        <p class="text-muted">All bookings have been processed</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Pending Tab -->
        <div class="tab-pane fade" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab" tabindex="0">
            <div class="row">
                @php
                    $pendingBookings = $upcomingBookings->where('status', 'pending');
                @endphp
                
                @forelse($pendingBookings as $booking)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm booking-card">
                        <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="action-dot" title="Action required"></span>
                                {{ $booking->facilityItem->item_code }}
                            </h5>
                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>
                        </div>
                        
                        <div class="card-body pt-2">
                            <div class="mb-2">
                                <i class="bi bi-clock text-muted me-1"></i>
                                <small class="text-muted">
                                    @if($booking->start_datetime instanceof \Carbon\Carbon)
                                        {{ $booking->start_datetime->format('M d, Y g:i A') }} - 
                                        {{ $booking->end_datetime->format('M d, Y g:i A') }}
                                    @else
                                        {{ date('M d, Y g:i A', strtotime($booking->start_datetime)) }} - 
                                        {{ date('M d, Y g:i A', strtotime($booking->end_datetime)) }}
                                    @endif
                                </small>
                            </div>
                            
                            <p class="card-text mb-2">
                                {{ \Illuminate\Support\Str::limit($booking->purpose, 100) }}
                            </p>
                            
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-person me-1"></i>
                                {{ $booking->user->name }}
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View Details & Take Action
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No pending bookings</h5>
                        <p class="text-muted">All bookings have been processed</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Return Submitted Tab -->
        <div class="tab-pane fade" id="return-pane" role="tabpanel" aria-labelledby="return-tab" tabindex="0">
            <div class="row">
                @forelse($returnSubmitted as $booking)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm booking-card">
                        <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="action-dot" title="Action required"></span>
                                {{ $booking->facilityItem->item_code ?? 'Item' }}
                            </h5>
                            <span class="badge bg-purple" style="color: white !important; display: inline-block !important;">
                                Return Submitted
                            </span>
                        </div>
                        
                        <div class="card-body pt-2">
                            <div class="mb-2">
                                <i class="bi bi-clock text-muted me-1"></i>
                                <small class="text-muted">
                                    @if(isset($booking->start_datetime))
                                        @if($booking->start_datetime instanceof \Carbon\Carbon)
                                            {{ $booking->start_datetime->format('M d, Y g:i A') }} - 
                                            {{ $booking->end_datetime->format('M d, Y g:i A') }}
                                        @else
                                            {{ date('M d, Y g:i A', strtotime($booking->start_datetime)) }} - 
                                            {{ date('M d, Y g:i A', strtotime($booking->end_datetime)) }}
                                        @endif
                                    @else
                                        Time not available
                                    @endif
                                </small>
                            </div>
                            
                            <p class="card-text mb-2">
                                {{ isset($booking->purpose) ? \Illuminate\Support\Str::limit($booking->purpose, 100) : 'No purpose provided' }}
                            </p>
                            
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-person me-1"></i>
                                {{ isset($booking->user) && isset($booking->user->name) ? $booking->user->name : 'User' }}
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View Details & Take Action
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No returns pending verification</h5>
                        <p class="text-muted">All equipment returns have been verified</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Custom Pagination (No SVGs) -->
    @if(isset($upcomingBookings) && method_exists($upcomingBookings, 'hasPages') && $upcomingBookings->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination custom-pagination">
                <!-- Previous Page Link -->
                @if($upcomingBookings->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Previous">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $upcomingBookings->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                    </li>
                @endif

                <!-- Page Numbers -->
                @php
                    $currentPage = $upcomingBookings->currentPage();
                    $lastPage = $upcomingBookings->lastPage();
                    $window = 1; // Pages to show on each side of current page
                @endphp

                <!-- First Page -->
                @if($currentPage > ($window + 2))
                    <li class="page-item">
                        <a class="page-link" href="{{ $upcomingBookings->url(1) }}">1</a>
                    </li>
                    @if($currentPage > ($window + 3))
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                <!-- Pages Around Current -->
                @for($i = max(1, $currentPage - $window); $i <= min($lastPage, $currentPage + $window); $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $upcomingBookings->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <!-- Last Page -->
                @if($currentPage < ($lastPage - $window - 1))
                    @if($currentPage < ($lastPage - $window - 2))
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $upcomingBookings->url($lastPage) }}">{{ $lastPage }}</a>
                    </li>
                @endif

                <!-- Next Page Link -->
                @if($upcomingBookings->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $upcomingBookings->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Next">&raquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif

    <!-- Calendar Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> Calendar View</h5>
        </div>
        <div class="card-body">
            <div id="bookingCalendar"></div>
        </div>
    </div>

<!-- Improved Legend -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-2">
        <h6 class="mb-0">Booking Status Legend</h6>
    </div>
    <div class="card-body py-2">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #FBBC05;"></span>
                        <span class="small">Pending</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #34A853;"></span>
                        <span class="small">Approved</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #EA4335;"></span>
                        <span class="small">Rejected</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #1A73E8;"></span>
                        <span class="small">Completed</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #5F6368;"></span>
                        <span class="small">Cancelled</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #FF9800;"></span>
                        <span class="small">Needs Return</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #9C27B0;"></span>
                        <span class="small">Return Submitted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendar initialization
    const calendarEl = document.getElementById('bookingCalendar');
    
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: @json($calendarBookings),
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            height: 'auto',
            themeSystem: 'bootstrap5',
            nowIndicator: true,
            navLinks: true,
            dayMaxEvents: true,
        });
        
        calendar.render();
    }
});
</script>
@endsection