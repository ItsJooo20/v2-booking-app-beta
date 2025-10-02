@extends('layout.navbar')

@section('title', 'Laporan Pemesanan')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Laporan</h1>
        {{-- (Opsional) Tambah tombol export cepat --}}
        {{-- <div class="d-flex gap-2">
            <a href="{{ route('reports.generate', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('reports.generate', array_merge(request()->all(), ['export' => 'xlsx'])) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Excel
            </a>
        </div> --}}
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('reports.generate') }}" method="GET" id="reportForm">
                @csrf

                {{-- Periode Laporan --}}
                <div class="row mb-3">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold">Jenis Periode <span class="text-danger">*</span></label>
                        <select class="form-select" name="date_range_type" id="dateRangeType" required>
                            <option value="day" @selected(old('date_range_type','day')==='day')>Single Day</option>
                            <option value="range" @selected(old('date_range_type')==='range')>Date Range</option>
                            <option value="month" @selected(old('date_range_type')==='month')>Month</option>
                            <option value="year" @selected(old('date_range_type')==='year')>Year</option>
                        </select>
                        <div class="form-text">Pilih mode periode laporan.</div>
                    </div>
                </div>

                {{-- Input tanggal dinamis --}}
                <div id="dayFields" class="date-input-group">
                    <div class="row mb-3">
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date_day" value="{{ old('date_day', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div id="rangeFields" class="date-input-group" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" name="date_range_start" value="{{ old('date_range_start', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="date_range_end" value="{{ old('date_range_end', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div id="monthFields" class="date-input-group" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label">Bulan</label>
                            <input type="month" class="form-control" name="date_month" value="{{ old('date_month', date('Y-m')) }}">
                        </div>
                    </div>
                </div>

                <div id="yearFields" class="date-input-group" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label">Tahun</label>
                            <input type="number" class="form-control" name="date_year" min="2000" max="2100" value="{{ old('date_year', date('Y')) }}">
                        </div>
                    </div>
                </div>

                <hr class="mb-4">

                {{-- Filter Tambahan --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected(old('status')==='pending')>Pending</option>
                            <option value="approved" @selected(old('status')==='approved')>Approved</option>
                            <option value="rejected" @selected(old('status')==='rejected')>Rejected</option>
                            <option value="completed" @selected(old('status')==='completed')>Completed</option>
                            <option value="cancelled" @selected(old('status')==='cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">User</label>
                        <select class="form-select" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id')==$user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">Facility Category</label>
                        <select class="form-select" name="category_id" id="categorySelect">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Memilih kategori akan menonaktifkan pilihan facility & item.</div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">Facility</label>
                        <select class="form-select" name="facility_id" id="facilitySelect">
                            <option value="">All Facilities</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}" @selected(old('facility_id')==$facility->id)>
                                    {{ $facility->name }} ({{ $facility->category->name }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Memilih facility menonaktifkan kategori & item.</div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">Facility Item</label>
                        <select class="form-select" id="facilityItemsSelect" name="facility_item_id">
                            <option value="">All Item</option>
                            @foreach($facilityItems as $item)
                                <option value="{{ $item->id }}"
                                        data-facility="{{ $item->facility_id }}"
                                        @selected(old('facility_item_id')==$item->id)>
                                    {{ $item->item_code }} ({{ $item->facility->name }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Memilih item menonaktifkan kategori & facility.</div>
                    </div>
                    {{-- (Opsional) Tambah filter role user atau durasi --}}
                    {{-- <div class="col-md-4 col-lg-3">
                        <label class="form-label">Minimal Durasi (jam)</label>
                        <input type="number" min="0" class="form-control" name="min_hours" value="{{ old('min_hours') }}">
                    </div> --}}
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('reports.generate') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dateRangeType = document.getElementById('dateRangeType');
    const groups = {
        day: document.getElementById('dayFields'),
        range: document.getElementById('rangeFields'),
        month: document.getElementById('monthFields'),
        year: document.getElementById('yearFields')
    };

    function updateDateFields() {
        Object.values(groups).forEach(g => g.style.display='none');
        const selected = dateRangeType.value;
        if (groups[selected]) groups[selected].style.display='block';
    }
    if (dateRangeType) {
        updateDateFields();
        dateRangeType.addEventListener('change', updateDateFields);
    }

    const categorySelect = document.getElementById('categorySelect');
    const facilitySelect = document.getElementById('facilitySelect');
    const facilityItemsSelect = document.getElementById('facilityItemsSelect');

    function disableAllExcept(except) {
        const map = {category: categorySelect, facility: facilitySelect, item: facilityItemsSelect};
        Object.entries(map).forEach(([key, el]) => {
            if (!el) return;
            if (key === except || !except) {
                el.disabled = false;
            } else {
                el.disabled = true;
            }
        });
    }

    // Apply initial locking if old values exist
    if (categorySelect.value) disableAllExcept('category');
    else if (facilitySelect.value) disableAllExcept('facility');
    else if (facilityItemsSelect.value) disableAllExcept('item');

    categorySelect.addEventListener('change', function() {
        if (this.value) {
            facilitySelect.value = '';
            facilityItemsSelect.value = '';
            disableAllExcept('category');
        } else {
            disableAllExcept(null);
        }
    });

    facilitySelect.addEventListener('change', function() {
        if (this.value) {
            categorySelect.value = '';
            facilityItemsSelect.value = '';
            disableAllExcept('facility');
        } else {
            disableAllExcept(null);
        }
    });

    facilityItemsSelect.addEventListener('change', function() {
        if (this.value) {
            categorySelect.value = '';
            facilitySelect.value = '';
            disableAllExcept('item');
        } else {
            disableAllExcept(null);
        }
    });
});
</script>
@endpush
@endsection