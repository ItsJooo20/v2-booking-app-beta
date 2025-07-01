@extends('layout.navbar')

@section('title', 'Submit Equipment Return')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Submit Equipment Return</h1>
        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Booking
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between">
                <h5 class="mb-0">{{ $booking->facilityItem->item_code }}</h5>
                <span class="badge 
                    @if($booking->status === 'approved') bg-success
                    @elseif($booking->status === 'needs return') bg-orange
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Facility:</strong> {{ $booking->facilityItem->facility->name }}</p>
                    <p><strong>Booking Period:</strong><br>
                        {{ $booking->start_datetime->format('M d, Y g:i A') }} - 
                        {{ $booking->end_datetime->format('M d, Y g:i A') }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Booked By:</strong> {{ $booking->user->name }}</p>
                    <p><strong>Purpose:</strong> {{ $booking->purpose }}</p>
                </div>
            </div>

            <hr>

            <form action="{{ route('bookings.return.submit', $booking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="return_photo" class="form-label">Return Photo <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('return_photo') is-invalid @enderror" 
                        id="return_photo" name="return_photo" accept="image/*" required>
                    <div class="form-text">Please take a clear photo of the equipment being returned.</div>
                    @error('return_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="user_condition" class="form-label">Equipment Condition <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_condition') is-invalid @enderror" 
                        id="user_condition" name="user_condition" required>
                        <option value="">Select condition...</option>
                        <option value="good">Good - No visible damage or issues</option>
                        <option value="minor_issues">Minor Issues - Still functional but has minor problems</option>
                        <option value="damaged">Damaged - Has significant damage or functionality issues</option>
                    </select>
                    @error('user_condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                        placeholder="Please provide any additional information about the condition or usage of the equipment..."></textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Submit Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection