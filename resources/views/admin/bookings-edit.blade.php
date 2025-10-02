@extends('layout.navbar')

@section('title', 'Edit Pemesanan')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-section { margin-bottom:2rem; }
    .form-section-title {
        font-size:1.05rem; font-weight:600;
        margin-bottom:1rem; padding-bottom:.5rem;
        border-bottom:1px solid rgba(0,0,0,.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Edit Pemesanan</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="bi bi-building me-2"></i>Fasilitas
                    </h5>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <input type="text" class="form-control"
                               value="{{ $booking->facilityItem->facility->name }}" readonly>
                        <input type="hidden" name="facility_id" value="{{ $booking->facilityItem->facility_id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <input type="text" class="form-control"
                               value="{{ $booking->facilityItem->item_code }}{{ $booking->facilityItem->notes ? ' ('.$booking->facilityItem->notes.')':'' }}"
                               readonly>
                        <input type="hidden" name="facility_item_id" value="{{ $booking->facility_item_id }}">
                    </div>
                </div>

                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="bi bi-calendar-event me-2"></i>Detail Waktu
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_datetime">Mulai</label>
                            <input type="datetime-local" id="start_datetime" name="start_datetime"
                                   class="form-control"
                                   value="{{ old('start_datetime', $booking->start_datetime->format('Y-m-d\TH:i')) }}" required>
                            @error('start_datetime')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_datetime">Selesai</label>
                            <input type="datetime-local" id="end_datetime" name="end_datetime"
                                   class="form-control"
                                   value="{{ old('end_datetime', $booking->end_datetime->format('Y-m-d\TH:i')) }}" required>
                            @error('end_datetime')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="purpose">Tujuan / Keperluan</label>
                        <textarea id="purpose" name="purpose" rows="3" class="form-control" required>{{ old('purpose', $booking->purpose) }}</textarea>
                        @error('purpose')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(in_array(Auth::user()->role, ['admin','headmaster']))
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="pending" {{ $booking->status=='pending'?'selected':'' }}>Pending</option>
                                <option value="approved" {{ $booking->status=='approved'?'selected':'' }}>Approved</option>
                                <option value="rejected" {{ $booking->status=='rejected'?'selected':'' }}>Rejected</option>
                                <option value="completed" {{ $booking->status=='completed'?'selected':'' }}>Completed</option>
                                <option value="cancelled" {{ $booking->status=='cancelled'?'selected':'' }}>Cancelled</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2"
                            onclick="window.location='{{ route('bookings.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        Perbarui
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#start_datetime", {
        enableTime:true,
        dateFormat:"Y-m-d H:i",
        minDate: "{{ $booking->is_upcoming ? 'today' : null }}",
    });
    flatpickr("#end_datetime", {
        enableTime:true,
        dateFormat:"Y-m-d H:i",
        minDate: "{{ $booking->is_upcoming ? 'today' : null }}",
    });
});
</script>
@endsection