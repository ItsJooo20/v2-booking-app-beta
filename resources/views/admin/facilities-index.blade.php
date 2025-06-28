@extends('layout.navbar')

@section('title', 'Facilities')

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
        max-width: 20px !important;
        max-height: 20px !important;
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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Facilities</h1>
        <a href="{{ route('facilities.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Facility
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('facilities.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Filter by Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('facilities.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($facilities as $facility)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ $highlightFacility == $facility->id ? 'border-success' : '' }}">
                    <!-- Make the image clickable to view facility items -->
                    <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}" class="text-decoration-none">
                        <div class="position-relative">
                            <img src="{{ $facility->getImageUrl() }}" class="card-img-top" alt="{{ $facility->name }}" style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                {{ $facility->items_count }} item(s)
                            </span>
                        </div>
                    </a>
                    <div class="card-body">
                        <!-- Make the title clickable to view facility items -->
                        <h5 class="card-title">
                            <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}" class="text-decoration-none text-dark">
                                {{ $facility->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small">{{ $facility->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $facility->category->name }}</span>
                            <div>
                                @if($facility->can_be_addon)
                                    <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Can be used as an add-on">Add-on</span>
                                @endif
                                @if($facility->can_have_addon)
                                    <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Can have add-ons">Has add-ons</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <div>
                                <!-- Add a "View Items" button -->
                                <a href="{{ route('facility-items.index', ['facility_id' => $facility->id]) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-box-seam me-1"></i> View Items
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this facility?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" {{ $facility->items_count > 0 ? 'disabled' : '' }}>
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

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $facilities->links() }}
    </div>

    @if($facilities->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted">No facilities found</p>
            <a href="{{ route('facilities.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-1"></i> Add First Facility
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .pagination svg {
        width: 20px;
        height: 20px;
        vertical-align: middle;
    }

    .page-item {
        display: flex;
        align-items: center;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        height: 38px;
    }
    
    .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .card:hover .card-img-top {
        transform: scale(1.03);
    }
</style>
@endpush

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush