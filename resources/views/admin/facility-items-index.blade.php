@extends('layout.navbar')

@section('title', 'Item Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Item Fasilitas</h1>
        <a href="{{ route('facility-items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Item
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

    <!-- Form Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('facility-items.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="facility_id" class="form-label">Filter Fasilitas</label>
                    <select class="form-select" id="facility_id" name="facility_id">
                        <option value="">Semua Fasilitas</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ request('facility_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('facility-items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($facilityItems as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($item->images->count() > 1)
                        <div class="custom-slider" id="slider-{{ $item->id }}">
                            <div class="slider-container">
                                @php
                                    $primaryImage = $item->images->firstWhere('is_primary', true) ?? $item->images->first();
                                    $orderedImages = $item->images->sortByDesc(fn($img) => $img->id === $primaryImage->id ? 1 : 0);
                                @endphp
                                @foreach($orderedImages as $index => $image)
                                    <div class="slider-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                        <img src="{{ $image->getImageUrl() }}" alt="{{ $item->item_code }}" class="slider-image">
                                    </div>
                                @endforeach
                            </div>
                            <button class="slider-control prev" data-slider="slider-{{ $item->id }}">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="slider-control next" data-slider="slider-{{ $item->id }}">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <div class="slider-dots">
                                @foreach($orderedImages as $index => $image)
                                    <span class="slider-dot {{ $index === 0 ? 'active' : '' }}" data-slider="slider-{{ $item->id }}" data-index="{{ $index }}"></span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <img src="{{ $item->getPrimaryImageUrl() }}" class="card-img-top" alt="{{ $item->item_code }}" style="height:200px;object-fit:cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $item->item_code }}</h5>
                        <p class="card-text text-muted small mb-2">{{ Str::limit($item->notes, 100) ?: '—' }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $item->facility->name }}</span>
                            @if($item->serial_number)
                                <small class="text-muted">SN: {{ $item->serial_number }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('facility-items.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <form action="{{ route('facility-items.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $facilityItems->appends(request()->query())->links() }}
    </div>

    @if($facilityItems->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted mb-2">Belum ada item fasilitas</p>
            <a href="{{ route('facility-items.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Item Pertama
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
    [aria-label="Next"] svg { width:20px;height:20px; }
    .page-link { line-height:1; padding:.5rem !important; display:flex !important; align-items:center !important; justify-content:center !important; }
    .custom-slider { position:relative; height:200px; overflow:hidden; }
    .slider-item { position:absolute; inset:0; width:100%; height:100%; opacity:0; display:none; transition:opacity .5s; }
    .slider-item.active { opacity:1; display:block; }
    .slider-image { width:100%; height:200px; object-fit:cover; }
    .slider-control { position:absolute; top:50%; transform:translateY(-50%); width:40px; height:40px; background:rgba(0,0,0,.3); color:#fff; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.3s; z-index:10; }
    .slider-control:hover { background:rgba(0,0,0,.6); }
    .slider-control.prev { left:0; border-radius:0 4px 4px 0; }
    .slider-control.next { right:0; border-radius:4px 0 0 4px; }
    .slider-dots { position:absolute; left:0; right:0; bottom:10px; display:flex; justify-content:center; gap:6px; z-index:10; }
    .slider-dot { width:10px; height:10px; background:rgba(255,255,255,.5); border-radius:50%; cursor:pointer; transition:.3s; }
    .slider-dot.active { background:#fff; transform:scale(1.2); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    function goToSlide(sliderId, index) {
        const slider = document.getElementById(sliderId);
        if (!slider) return;
        const slides = slider.querySelectorAll('.slider-item');
        const dots = slider.querySelectorAll('.slider-dot');
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        if (slides[index]) slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
    }
    document.querySelectorAll('.slider-control.prev').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.slider;
            const slider = document.getElementById(id);
            const active = slider.querySelector('.slider-item.active');
            const slides = slider.querySelectorAll('.slider-item');
            let curr = parseInt(active.dataset.index);
            let prev = (curr - 1 + slides.length) % slides.length;
            goToSlide(id, prev);
        });
    });
    document.querySelectorAll('.slider-control.next').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.slider;
            const slider = document.getElementById(id);
            const active = slider.querySelector('.slider-item.active');
            const slides = slider.querySelectorAll('.slider-item');
            let curr = parseInt(active.dataset.index);
            let next = (curr + 1) % slides.length;
            goToSlide(id, next);
        });
    });
    document.querySelectorAll('.slider-dot').forEach(dot => {
        dot.addEventListener('click', function(){
            goToSlide(this.dataset.slider, parseInt(this.dataset.index));
        });
    });
});
</script>
@endpush