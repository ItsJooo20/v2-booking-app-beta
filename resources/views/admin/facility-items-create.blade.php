@extends('layout.navbar')

@section('title', 'Create Facility Item')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create Facility Item</h1>
        <a href="{{ route('facility-items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facility-items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="item_code" class="form-label">Item Code</label>
                            <input type="text" class="form-control @error('item_code') is-invalid @enderror" id="item_code" name="item_code" value="{{ old('item_code') }}" required>
                            @error('item_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter by Category</label>
                            <select class="form-select" id="category_filter">
                                <option value="">All Categories</option>
                                @foreach($facilities->pluck('category')->unique('id') as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select a category to filter facilities</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="facility_id" class="form-label">Facility</label>
                            <select class="form-select @error('facility_id') is-invalid @enderror" id="facility_id" name="facility_id" required>
                                <option value="">Select a facility</option>
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}" data-category="{{ $facility->category->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                                        {{ $facility->name }} ({{ $facility->category->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Serial Number</label>
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="5">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Images (Upload up to 4)</label>
                    <div class="input-group mb-3">
                        <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                        <label class="input-group-text" for="images">Browse</label>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Upload up to 4 images (JPEG, PNG, JPG, GIF, max 2MB each)</div>
                    
                    <div id="image-preview-container" class="row mt-3"></div>
                    
                    <input type="hidden" name="primary_image" id="primary_image" value="0">
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Create Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category filter functionality
        const categoryFilter = document.getElementById('category_filter');
        const facilitySelect = document.getElementById('facility_id');
        const facilityOptions = Array.from(facilitySelect.options);
        
        categoryFilter.addEventListener('change', function() {
            const selectedCategoryId = this.value;
            
            // Remove all current options
            while (facilitySelect.options.length > 0) {
                facilitySelect.remove(0);
            }
            
            // Add the placeholder option
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.text = 'Select a facility';
            facilitySelect.add(placeholderOption);
            
            // Add filtered options
            facilityOptions.forEach(option => {
                if (option.value === '' || !selectedCategoryId || option.dataset.category === selectedCategoryId) {
                    facilitySelect.add(option.cloneNode(true));
                }
            });
        });
        
        // Image preview functionality
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        const primaryImageInput = document.getElementById('primary_image');
        let imageFiles = [];
        
        imageInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            imageFiles = Array.from(this.files).slice(0, 4); // Limit to 4 images
            
            if (imageFiles.length > 0) {
                imageFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6 mb-3';
                    
                    reader.onload = function(e) {
                        col.innerHTML = `
                            <div class="card h-100 ${index === 0 ? 'border-primary' : ''}">
                                <img src="${e.target.result}" class="card-img-top" alt="Preview" style="height: 120px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="form-check">
                                        <input class="form-check-input primary-image-radio" type="radio" name="primary_image_radio" id="primary_image_${index}" value="${index}" ${index === 0 ? 'checked' : ''}>
                                        <label class="form-check-label" for="primary_image_${index}">
                                            Primary Image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        previewContainer.appendChild(col);
                        
                        // Re-initialize radio buttons after adding to DOM
                        const radioButtons = document.querySelectorAll('.primary-image-radio');
                        radioButtons.forEach(radio => {
                            radio.addEventListener('change', function() {
                                // Update hidden input with selected index
                                primaryImageInput.value = this.value;
                                
                                // Update card borders
                                document.querySelectorAll('#image-preview-container .card').forEach(card => {
                                    card.classList.remove('border-primary');
                                });
                                this.closest('.card').classList.add('border-primary');
                            });
                        });
                    }
                    
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
@endpush