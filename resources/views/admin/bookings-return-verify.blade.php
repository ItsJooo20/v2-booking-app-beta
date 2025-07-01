@extends('layout.navbar')

@section('title', 'Verify Equipment Return')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Verify Equipment Return</h1>
        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Booking
        </a>
    </div>

    @if(session('success'))
        <div class="alert mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-red mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Return Details</h5>
                    <div class="mb-3">
                        <div class="text-muted">Item</div>
                        <div class="fw-medium">{{ $booking->facilityItem->item_code }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Facility</div>
                        <div class="fw-medium">{{ $booking->facilityItem->facility->name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Booking Period</div>
                        <div class="fw-medium">
                            @php
                                $start = $booking->start_datetime instanceof \Carbon\Carbon ? 
                                    $booking->start_datetime : 
                                    \Carbon\Carbon::parse($booking->start_datetime);
                                    
                                $end = $booking->end_datetime instanceof \Carbon\Carbon ? 
                                    $booking->end_datetime : 
                                    \Carbon\Carbon::parse($booking->end_datetime);
                            @endphp
                            {{ $start->format('M d, Y - g:i A') }} to {{ $end->format('M d, Y - g:i A') }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Returned by</div>
                        <div class="fw-medium">{{ $booking->user->name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">Return Date</div>
                        <div class="fw-medium">
                            @php
                                $returnDate = $booking->equipmentReturn->return_date instanceof \Carbon\Carbon ? 
                                    $booking->equipmentReturn->return_date : 
                                    \Carbon\Carbon::parse($booking->equipmentReturn->return_date);
                            @endphp
                            {{ $returnDate->format('M d, Y - g:i A') }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted">User Reported Condition</div>
                        <div class="fw-medium">
                            <span class="badge 
                                @if($booking->equipmentReturn->user_condition === 'good') bg-success
                                @elseif($booking->equipmentReturn->user_condition === 'minor_issues') bg-warning text-dark
                                @elseif($booking->equipmentReturn->user_condition === 'damaged') bg-danger
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->equipmentReturn->user_condition)) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($booking->equipmentReturn->notes)
                    <div class="mb-0">
                        <div class="text-muted">User Notes</div>
                        <div class="fw-medium">{{ $booking->equipmentReturn->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Return Photo</h5>
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $booking->equipmentReturn->return_photo_path) }}" 
                             alt="Equipment Return Photo" class="img-fluid rounded" 
                             style="max-height: 300px;">
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Verify Return</h5>
                    <form action="{{ route('bookings.return.verify', $booking->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="condition_status" class="form-label">Verified Condition <span class="text-danger">*</span></label>
                            <select class="form-select @error('condition_status') is-invalid @enderror" 
                                id="condition_status" name="condition_status" required>
                                <option value="">Select condition...</option>
                                <option value="good">Good - No damage or issues</option>
                                <option value="damaged">Damaged - Has damage or functionality issues</option>
                                <option value="missing">Missing - Equipment not returned</option>
                            </select>
                            @error('condition_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="notes" class="form-label">Admin Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                placeholder="Add any notes about the condition or issues with the returned equipment..."></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Verify Return
                            </button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection