@extends('layout.navbar')

@section('title', 'Facility Categories')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Facility Categories</h1>
        <a href="{{ route('facility-categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Category
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

    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ $highlightCategory == $category->id ? 'border-success' : '' }}">
                    <!-- Make the image clickable to view facilities in this category -->
                    <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                        <div class="position-relative">
                            <img src="{{ $category->getImageUrl() }}" class="card-img-top" alt="{{ $category->name }}" style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                {{ $category->facilities_count }} {{ Str::plural('facility', $category->facilities_count) }}
                            </span>
                        </div>
                    </a>
                    <div class="card-body">
                        <!-- Make the title clickable -->
                        <h5 class="card-title">
                            <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}" class="text-decoration-none text-dark">
                                {{ $category->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small">{{ $category->description }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                @if($category->requires_return)
                                    <span class="badge bg-info" data-bs-toggle="tooltip" title="Items require return">Requires Return</span>
                                @endif
                                @if($category->return_photo_required)
                                    <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Photo needed for return">Return Photo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <div>
                                <!-- Add a "View Facilities" button -->
                                <a href="{{ route('facilities.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-collection me-1"></i> View Facilities
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('facility-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('facility-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" {{ $category->facilities_count > 0 ? '' : '' }}>
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
        {{ $categories->links() }}
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted">No facility categories found</p>
            <a href="{{ route('facility-categories.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-1"></i> Add First Category
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Fix oversized pagination arrows */
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
    
    /* Target specifically the pagination container */
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