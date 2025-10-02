@extends('layout.navbar')

@section('title', 'Verify Equipment Return')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Verifikasi Pengembalian Peralatan</h1>
        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Booking
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- Detail Pengembalian --}}
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Detail Pengembalian</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Item</div>
                        <div class="fw-medium">{{ $booking->facilityItem->item_code }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Fasilitas</div>
                        <div class="fw-medium">{{ $booking->facilityItem->facility->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Periode Booking</div>
                        <div class="fw-medium">
                            @php
                                $start = $booking->start_datetime instanceof \Carbon\Carbon ? $booking->start_datetime : \Carbon\Carbon::parse($booking->start_datetime);
                                $end = $booking->end_datetime instanceof \Carbon\Carbon ? $booking->end_datetime : \Carbon\Carbon::parse($booking->end_datetime);
                            @endphp
                            {{ $start->format('M d, Y - g:i A') }} to {{ $end->format('M d, Y - g:i A') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Returned by</div>
                        <div class="fw-medium">{{ $booking->user->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Return Date</div>
                        <div class="fw-medium">
                            @php
                                $returnDate = $booking->equipmentReturn->return_date instanceof \Carbon\Carbon
                                    ? $booking->equipmentReturn->return_date
                                    : \Carbon\Carbon::parse($booking->equipmentReturn->return_date);
                            @endphp
                            {{ $returnDate->format('M d, Y - g:i A') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">User Reported Condition</div>
                        <div class="fw-medium">
                            <span class="badge
                                @if($booking->equipmentReturn->user_condition === 'good') bg-success
                                @elseif($booking->equipmentReturn->user_condition === 'minor_issues') bg-warning text-dark
                                @elseif($booking->equipmentReturn->user_condition === 'damaged') bg-danger
                                @endif">
                                {{ ucfirst(str_replace('_',' ',$booking->equipmentReturn->user_condition)) }}
                            </span>
                        </div>
                    </div>
                    @if($booking->equipmentReturn->notes)
                        <div class="mb-0">
                            <div class="text-muted small">User Notes</div>
                            <div class="fw-medium">{{ $booking->equipmentReturn->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Foto & Form Verifikasi --}}
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Return Photo</h5>
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $booking->equipmentReturn->return_photo_path) }}"
                             alt="Equipment Return Photo"
                             class="img-fluid rounded"
                             style="max-height:300px;">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Verify Return</h5>
                    <form action="{{ route('bookings.return.verify', $booking->id) }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="condition_status" class="form-label">Verified Condition <span class="text-danger">*</span></label>
                            <select id="condition_status"
                                    name="condition_status"
                                    class="form-select @error('condition_status') is-invalid @enderror"
                                    required>
                                <option value="">Select condition...</option>
                                <option value="good" {{ old('condition_status')=='good'?'selected':'' }}>Good - No damage or issues</option>
                                <option value="damaged" {{ old('condition_status')=='damaged'?'selected':'' }}>Damaged - Has damage or functionality issues</option>
                                <option value="missing" {{ old('condition_status')=='missing'?'selected':'' }}>Missing - Equipment not returned</option>
                            </select>
                            @error('condition_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Admin Notes</label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Add any notes about the condition or issues...">{{ old('notes') }}</textarea>
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