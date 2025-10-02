@extends('layout.navbar')

@section('title', 'Fasilitas')

@push('styles')
<style>
    .pagination svg,
    nav svg,
    .page-item svg,
    .page-link svg,
    [aria-label="Previous"] svg,
    [aria-label="Next"] svg {
        width: 20px !important;
        height: 20px !important;
    }
    .page-link {
        line-height: 1;
        padding: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    nav[aria-label="Pagination Navigation"] {
        max-width: 100%;
        overflow: hidden;
    }
    .pagination .page-item .page-link {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-img-top {
        transition: transform .3s ease;
    }
    .card:hover .card-img-top {
        transform: scale(1.03);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Fasilitas</h1>
        <a href="{{ route('facilities.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('facilities.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Filter Kategori</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('facilities.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($facilities as $facility)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ (isset($highlightFacility) && $highlightFacility == $facility->id) ? 'border-success' : '' }}">
                    <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}" class="text-decoration-none">
                        <div class="position-relative">
                            <img src="{{ $facility->getImageUrl() }}"
                                 class="card-img-top"
                                 alt="{{ $facility->name }}"
                                 style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                {{ $facility->items_count }} item
                            </span>
                        </div>
                    </a>
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}"
                               class="text-decoration-none text-dark">
                                {{ $facility->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small mb-3">{{ $facility->description ?: '—' }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $facility->category->name }}</span>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($facility->can_be_addon)
                                    <span class="badge bg-secondary"
                                          data-bs-toggle="tooltip"
                                          title="Dapat menjadi tambahan untuk fasilitas lain">Add-on</span>
                                @endif
                                @if($facility->can_have_addon)
                                    <span class="badge bg-secondary"
                                          data-bs-toggle="tooltip"
                                          title="Dapat memiliki fasilitas tambahan">Memiliki Add-on</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}"
                               class="btn btn-sm btn-success">
                                <i class="bi bi-box-seam me-1"></i> Lihat Item
                            </a>
                            <div class="d-flex">
                                <a href="{{ route('facilities.edit', $facility->id) }}"
                                   class="btn btn-sm btn-outline-primary me-1"
                                   data-bs-toggle="tooltip"
                                   title="Edit Fasilitas">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('facilities.destroy', $facility->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus fasilitas ini? @if($facility->items_count>0) Fasilitas masih memiliki item terkait.@endif')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            @if($facility->items_count > 0)
                                                disabled
                                                data-bs-toggle="tooltip"
                                                title="Tidak bisa dihapus: masih ada item"
                                            @else
                                                data-bs-toggle="tooltip"
                                                title="Hapus Fasilitas"
                                            @endif
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $facilities->links() }}
    </div>

    @if($facilities->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted mb-2">Belum ada fasilitas</p>
            <a href="{{ route('facilities.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas Pertama
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
</script>
@endpush