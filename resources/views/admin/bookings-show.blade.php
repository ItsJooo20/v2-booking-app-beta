@extends('layout.navbar')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="container-fluid py-4">
    <!-- Header with back button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Booking #{{ $booking->id }}</h1>
            <div class="text-muted">
                <a href="{{ route('bookings.index') }}" class="text-decoration-none">Bookings</a>
                <i class="bi bi-chevron-right mx-1 small"></i>
                <span>Details</span>
            </div>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('success') || session('error'))
        <div class="alert {{ session('error') ? 'alert-red' : 'alert' }} mb-4">
            <i class="bi bi-{{ session('error') ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Booking Information</h5>
                        <span class="badge 
                            @if($booking->status === 'approved') bg-success
                            @elseif($booking->status === 'pending') bg-warning text-dark
                            @elseif($booking->status === 'rejected') bg-danger
                            @elseif($booking->status === 'completed') bg-primary
                            @elseif($booking->status === 'cancelled') bg-secondary
                            @elseif($booking->status === 'needs return') bg-warning
                            @elseif($booking->status === 'return submitted') bg-info
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>

                    <!-- Basic booking information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted">Facility</div>
                                <div class="fw-medium">{{ $booking->facilityItem->facility->name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted">Item Code</div>
                                <div class="fw-medium">{{ $booking->facilityItem->item_code }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted">Start</div>
                                <div class="fw-medium">
                                    @if($booking->start_datetime instanceof \Carbon\Carbon)
                                        {{ $booking->start_datetime->format('M d, Y - g:i A') }}
                                    @else
                                        {{ date('M d, Y - g:i A', strtotime($booking->start_datetime)) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted">Booked by</div>
                                <div class="fw-medium">{{ $booking->user->name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted">End</div>
                                <div class="fw-medium">
                                    @if($booking->end_datetime instanceof \Carbon\Carbon)
                                        {{ $booking->end_datetime->format('M d, Y - g:i A') }}
                                    @else
                                        {{ date('M d, Y - g:i A', strtotime($booking->end_datetime)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted">Duration</div>
                                <div class="fw-medium">
                                    @php
                                        $start = $booking->start_datetime instanceof \Carbon\Carbon ? 
                                            $booking->start_datetime : 
                                            \Carbon\Carbon::parse($booking->start_datetime);
                                            
                                        $end = $booking->end_datetime instanceof \Carbon\Carbon ? 
                                            $booking->end_datetime : 
                                            \Carbon\Carbon::parse($booking->end_datetime);
                                            
                                        $duration = $start->diff($end);
                                        $hours = $duration->h + ($duration->days * 24);
                                        $minutes = $duration->i;
                                        echo $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ' . $minutes . ' minute' . ($minutes != 1 ? 's' : '');
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted">Purpose</div>
                        <div class="fw-medium">{{ $booking->purpose }}</div>
                    </div>

                    <!-- Equipment return info if relevant -->
                    @if($booking->equipmentReturn)
                        <hr class="my-4">
                        <div class="mb-3">
                            <h6>Equipment Return</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted">Return Date</div>
                                        <div class="fw-medium">
                                            @if($booking->equipmentReturn->return_date instanceof \Carbon\Carbon)
                                                {{ $booking->equipmentReturn->return_date->format('M d, Y - g:i A') }}
                                            @else
                                                {{ date('M d, Y - g:i A', strtotime($booking->equipmentReturn->return_date)) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted">Condition</div>
                                        <div class="fw-medium">
                                            <span class="badge 
                                                @if($booking->equipmentReturn->condition_status === 'good') bg-success
                                                @elseif($booking->equipmentReturn->condition_status === 'damaged') bg-danger
                                                @elseif($booking->equipmentReturn->condition_status === 'missing') bg-dark
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($booking->equipmentReturn->condition_status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if($booking->equipmentReturn->notes)
                                    <div class="mb-3">
                                        <div class="text-muted">Notes</div>
                                        <div class="fw-medium">{{ $booking->equipmentReturn->notes }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- User Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">User Information</h5>
                    
                    <div class="mb-3">
                        <div class="text-muted">Name</div>
                        <div class="fw-medium">{{ $booking->user->name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Role</div>
                        <div class="fw-medium">{{ $booking->user->role }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Email</div>
                        <div class="fw-medium">{{ $booking->user->email }}</div>
                    </div>
                    
                    @if($booking->user->phone)
                    <div class="mb-0">
                        <div class="text-muted">Phone</div>
                        <div class="fw-medium">{{ $booking->user->phone }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Timeline</h5>

                    <div class="mb-3">
                        <div class="fw-medium">
                            @if($booking->created_at instanceof \Carbon\Carbon)
                                {{ $booking->created_at->format('M d, Y - g:i A') }}
                            @else
                                {{ date('M d, Y - g:i A', strtotime($booking->created_at)) }}
                            @endif
                        </div>
                        <div>Booking created by {{ $booking->user->name }}</div>
                    </div>

                    @if($booking->status != 'pending')
                    <div class="mb-3">
                        <div class="fw-medium">
                            @if($booking->updated_at instanceof \Carbon\Carbon)
                                {{ $booking->updated_at->format('M d, Y - g:i A') }}
                            @else
                                {{ date('M d, Y - g:i A', strtotime($booking->updated_at)) }}
                            @endif
                        </div>
                        <div>Status changed to {{ ucfirst(str_replace('_', ' ', $booking->status)) }}</div>
                    </div>
                    @endif

                    @if($booking->equipmentReturn)
                    <div class="mb-3">
                        <div class="fw-medium">
                            @if($booking->equipmentReturn->created_at instanceof \Carbon\Carbon)
                                {{ $booking->equipmentReturn->created_at->format('M d, Y - g:i A') }}
                            @else
                                {{ date('M d, Y - g:i A', strtotime($booking->equipmentReturn->created_at)) }}
                            @endif
                        </div>
                        <div>Equipment return submitted</div>
                    </div>

                    @if($booking->equipmentReturn->verified_at)
                    <div class="mb-0">
                        <div class="fw-medium">
                            @if($booking->equipmentReturn->verified_at instanceof \Carbon\Carbon)
                                {{ $booking->equipmentReturn->verified_at->format('M d, Y - g:i A') }}
                            @else
                                {{ date('M d, Y - g:i A', strtotime($booking->equipmentReturn->verified_at)) }}
                            @endif
                        </div>
                        <div>Return verified as {{ $booking->equipmentReturn->condition_status }}</div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Actions</h5>
                    
                    <!-- Admin actions for pending bookings -->
                    @if(in_array(Auth::user()->role, ['admin', 'headmaster']) && $booking->status == 'pending')
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-2"></i>Approve
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-2"></i>Reject
                            </button>
                        </div>
                    @endif
                    
                    <!-- Return actions -->
                    @if($booking->status === 'needs return' && (Auth::id() == $booking->user_id || in_array(Auth::user()->role, ['admin', 'headmaster'])))
                        <a href="{{ route('bookings.return.show', $booking->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-box-arrow-in-down me-2"></i>Submit Return
                        </a>
                    @endif
                    
                    @if($booking->status === 'return submitted' && in_array(Auth::user()->role, ['admin', 'headmaster']))
                        <a href="{{ route('bookings.return.verify.show', $booking->id) }}" class="btn btn-info w-100 mb-2">
                            <i class="bi bi-check-circle me-2"></i>Verify Return
                        </a>
                    @endif
                    
                    <!-- Edit/Cancel actions -->
                    @if(in_array($booking->status, ['pending', 'approved']) && (Auth::id() == $booking->user_id || in_array(Auth::user()->role, ['admin', 'headmaster'])))
                        <div class="d-grid gap-2 mt-2">
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Cancel Booking
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Confirm Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this booking for <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p>Approving this booking will make the facility unavailable for the requested time period.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('bookings.approve', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Confirm Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this booking for <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p>This will notify the user that their booking request has been denied.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('bookings.reject', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Reject Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Confirm Cancel -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking for <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep Booking</button>
                    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection