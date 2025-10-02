@extends('layout.navbar')

@section('title', 'Buat Pemesanan')

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
        <h1 class="h3 mb-0">Buat Pemesanan Baru</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf

                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="bi bi-building me-2"></i>Pilih Fasilitas
                    </h5>
                    <div class="mb-3">
                        <label class="form-label" for="facility_id">Fasilitas</label>
                        <select class="form-select" id="facility_id" name="facility_id" required>
                            <option value="">-- Pilih Fasilitas --</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected':'' }}>
                                    {{ $facility->name }} ({{ $facility->category->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="facility_item_id">Item Fasilitas</label>
                        <select class="form-select" id="facility_item_id" name="facility_item_id" required>
                            <option value="">-- Pilih Item --</option>
                            @foreach($facilityItems as $item)
                                <option value="{{ $item->id }}"
                                        data-facility="{{ $item->facility_id }}"
                                        {{ old('facility_item_id') == $item->id ? 'selected':'' }}>
                                    {{ $item->item_code }} ({{ $item->facility->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('facility_item_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="bi bi-calendar-event me-2"></i>Detail Pemesanan
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_datetime">Mulai</label>
                            <input type="datetime-local" id="start_datetime" name="start_datetime"
                                   class="form-control" value="{{ old('start_datetime') }}" required>
                            @error('start_datetime')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_datetime">Selesai</label>
                            <input type="datetime-local" id="end_datetime" name="end_datetime"
                                   class="form-control" value="{{ old('end_datetime') }}" required>
                            @error('end_datetime')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="purpose">Tujuan / Keperluan</label>
                        <textarea id="purpose" name="purpose" rows="3" class="form-control" required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Permintaan pemesanan akan direview oleh admin. Anda akan menerima notifikasi setelah disetujui.
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2"
                            onclick="window.location='{{ route('bookings.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        Kirim Permintaan
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
    flatpickr("#start_datetime", { enableTime:true, dateFormat:"Y-m-d H:i", minDate:"today" });
    flatpickr("#end_datetime", { enableTime:true, dateFormat:"Y-m-d H:i", minDate:"today" });

    const facilitySelect = document.getElementById('facility_id');
    const itemSelect = document.getElementById('facility_item_id');

    facilitySelect.addEventListener('change', () => {
        const fid = facilitySelect.value;
        const options = itemSelect.querySelectorAll('option');
        options.forEach(opt => {
            if (opt.value === '') {
                opt.hidden = false;
                return;
            }
            const match = opt.getAttribute('data-facility') === fid;
            opt.hidden = fid && !match;
        });
        itemSelect.value = '';
    });
});
</script>
@endsection