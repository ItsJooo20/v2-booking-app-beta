@extends('layout.navbar')

@section('title', 'Facility Items')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Facility Items</h1>
        <a href="{{ route('facility-items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Item
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

    <!-- Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('facility-items.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="facility_id" class="form-label">Filter by Facility</label>
                    <select class="form-select" id="facility_id" name="facility_id">
                        <option value="">All Facilities</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ request('facility_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('facility-items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($facilityItems as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <!-- Custom Image Slider -->
                    @if($item->images->count() > 1)
                        <div class="custom-slider" id="slider-{{ $item->id }}">
                            <div class="slider-container">
                                @php
                                    // Find the primary image first
                                    $primaryImage = $item->images->firstWhere('is_primary', true);
                                    // If no primary image exists, use the first image
                                    if (!$primaryImage) {
                                        $primaryImage = $item->images->first();
                                    }
                                    
                                    // Reorder images to put primary first
                                    $orderedImages = $item->images->sortByDesc(function($image) use ($primaryImage) {
                                        return $image->id === $primaryImage->id ? 1 : 0;
                                    });
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
                        <!-- Single image or placeholder -->
                        <img src="{{ $item->getPrimaryImageUrl() }}" class="card-img-top" alt="{{ $item->item_code }}" style="height: 200px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->item_code }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($item->notes, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge bg-info">{{ $item->facility->name }}</span>
                            @if($item->serial_number)
                                <small class="text-muted">SN: {{ $item->serial_number }}</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('facility-items.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                            </div>
                            <div>
                                <form action="{{ route('facility-items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Delete
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
        {{ $facilityItems->appends(request()->query())->links() }}
    </div>

    @if($facilityItems->isEmpty())
        <div class="text-center py-5">
            <p class="fs-5 text-muted">No facility items found</p>
            <a href="{{ route('facility-items.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-1"></i> Add First Item
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
    
    /* Custom Slider Styles */
    .custom-slider {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .slider-container {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .slider-item {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.5s ease;
        display: none;
    }
    
    .slider-item.active {
        opacity: 1;
        display: block;
    }
    
    .slider-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .slider-control {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background-color: rgba(0,0,0,0.3);
        border: none;
        color: white;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: background-color 0.3s;
    }
    
    .slider-control:hover {
        background-color: rgba(0,0,0,0.6);
    }
    
    .slider-control.prev {
        left: 0;
        border-radius: 0 4px 4px 0;
    }
    
    .slider-control.next {
        right: 0;
        border-radius: 4px 0 0 4px;
    }
    
    .slider-dots {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 6px;
        z-index: 10;
    }
    
    .slider-dot {
        width: 10px;
        height: 10px;
        background-color: rgba(255,255,255,0.5);
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.3s, background-color 0.3s;
    }
    
    .slider-dot.active {
        background-color: white;
        transform: scale(1.2);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Custom slider implementation - no dependency on Bootstrap
        
        // Initialize all sliders
        const sliders = document.querySelectorAll('.custom-slider');
        
        // Function to move to a specific slide
        function goToSlide(sliderId, index) {
            const slider = document.getElementById(sliderId);
            if (!slider) return;
            
            // Get all slides in this slider
            const slides = slider.querySelectorAll('.slider-item');
            const dots = slider.querySelectorAll('.slider-dot');
            
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Deactivate all dots
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Show the selected slide
            if (slides[index]) {
                slides[index].classList.add('active');
            }
            
            // Activate the corresponding dot
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }
        
        // Add event listeners to previous buttons
        const prevButtons = document.querySelectorAll('.slider-control.prev');
        prevButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sliderId = this.getAttribute('data-slider');
                const slider = document.getElementById(sliderId);
                if (!slider) return;
                
                const activeSlide = slider.querySelector('.slider-item.active');
                const slides = slider.querySelectorAll('.slider-item');
                
                if (!activeSlide) return;
                
                let currentIndex = parseInt(activeSlide.getAttribute('data-index'));
                let prevIndex = currentIndex - 1;
                
                // Loop to the last slide if at the beginning
                if (prevIndex < 0) {
                    prevIndex = slides.length - 1;
                }
                
                goToSlide(sliderId, prevIndex);
            });
        });
        
        // Add event listeners to next buttons
        const nextButtons = document.querySelectorAll('.slider-control.next');
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sliderId = this.getAttribute('data-slider');
                const slider = document.getElementById(sliderId);
                if (!slider) return;
                
                const activeSlide = slider.querySelector('.slider-item.active');
                const slides = slider.querySelectorAll('.slider-item');
                
                if (!activeSlide) return;
                
                let currentIndex = parseInt(activeSlide.getAttribute('data-index'));
                let nextIndex = currentIndex + 1;
                
                // Loop to the first slide if at the end
                if (nextIndex >= slides.length) {
                    nextIndex = 0;
                }
                
                goToSlide(sliderId, nextIndex);
            });
        });
        
        // Add event listeners to dots
        const dots = document.querySelectorAll('.slider-dot');
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const sliderId = this.getAttribute('data-slider');
                const index = parseInt(this.getAttribute('data-index'));
                
                goToSlide(sliderId, index);
            });
        });
        
        console.log('Custom slider initialization complete. Found ' + sliders.length + ' sliders.');
    });
</script>
@endpush