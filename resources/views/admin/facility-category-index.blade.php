@extends('layout.navbar')

@section('title', 'Kategori Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Kategori Fasilitas</h1>
        <a href="{{ route('facility-categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
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

    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ (isset($highlightCategory) && $highlightCategory == $category->id) ? 'border-success' : '' }}">
                    <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                        <div class="position-relative">
                            <img src="{{ $category->getImageUrl() }}"
                                 class="card-img-top"
                                 alt="{{ $category->name }}"
                                 style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                {{ $category->facilities_count }} fasilitas
                            </span>
                        </div>
                    </a>
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}"
                               class="text-decoration-none text-dark">
                                {{ $category->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small mb-3">{{ $category->description ?: '—' }}</p>

                        <div class="d-flex flex-wrap gap-2">
                            @if($category->requires_return)
                                <span class="badge bg-info"
                                      data-bs-toggle="tooltip"
                                      title="Item dalam kategori ini harus dikembalikan setelah dipakai">
                                    Perlu Dikembalikan
                                </span>
                            @endif
                            @if($category->return_photo_required)
                                <span class="badge bg-secondary"
                                      data-bs-toggle="tooltip"
                                      title="Perlu foto saat pengembalian">
                                    Foto Pengembalian
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}"
                               class="btn btn-sm btn-success">
                                <i class="bi bi-collection me-1"></i> Lihat Fasilitas
                            </a>

                            <div class="d-flex">
                                <a href="{{ route('facility-categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-outline-primary me-1"
                                   data-bs-toggle="tooltip"
                                   title="Edit Kategori">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('facility-categories.destroy', $category->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini? @if($category->facilities_count > 0) Kategori masih memiliki fasilitas terkait.@endif')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            @if($category->facilities_count > 0)
                                                disabled
                                                data-bs-toggle="tooltip"
                                                title="Tidak dapat dihapus: masih ada fasilitas"
                                            @else
                                                data-bs-toggle="tooltip"
                                                title="Hapus Kategori"
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
        {{ $categories->links() }}
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted mb-2">Belum ada kategori fasilitas</p>
            <a href="{{ route('facility-categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kategori Pertama
            </a>
        </div>
    @endif
</div>
@endsection

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
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
    }
    nav[aria-label="Pagination Navigation"] {
        max-width: 100%;
        overflow: hidden;
    }
    .pagination .page-item .page-link {
        width: 38px;
        height: 38px;
    }
    .card-img-top { transition: transform .3s ease; }
    .card:hover .card-img-top { transform: scale(1.03); }
</style>
@endpush

@push('scripts')
<script>
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
</script>
@endpush