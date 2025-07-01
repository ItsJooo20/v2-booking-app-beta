@extends('layout.navbar')

@section('title', 'Edit Facility Item')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Facility Item</h1>
        <a href="{{ route('facility-items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facility-items.update', $facilityItem->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="item_code" class="form-label">Item Code</label>
                            <input type="text" class="form-control @error('item_code') is-invalid @enderror" id="item_code" name="item_code" value="{{ old('item_code', $facilityItem->item_code) }}" required>
                            @error('item_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter by Category</label>
                            <select class="form-select" id="category_filter">
                                <option value="">All Categories</option>
                                @foreach($facilities->pluck('category')->unique('id') as $category)
                                    <option value="{{ $category->id }}" {{ $facilityItem->facility->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Select a category to filter facilities</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="facility_id" class="form-label">Facility</label>
                            <select class="form-select @error('facility_id') is-invalid @enderror" id="facility_id" name="facility_id" required>
                                <option value="">Select a facility</option>
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}" data-category="{{ $facility->category->id }}" {{ old('facility_id', $facilityItem->facility_id) == $facility->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number', $facilityItem->serial_number) }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="7">{{ old('notes', $facilityItem->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Current Images Section -->
                <div class="mb-4">
                    <label class="form-label">Current Images</label>
                    <div class="row" id="current-images-container">
                        @foreach($facilityItem->images as $image)
                            <div class="col-md-3 col-6 mb-3" id="image-container-{{ $image->id }}">
                                <div class="card h-100 {{ $image->is_primary ? 'border-primary' : '' }}">
                                    <img src="{{ $image->getImageUrl() }}" class="card-img-top" alt="Image" style="height: 120px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between">
                                            <div class="form-check">
                                                <input class="form-check-input existing-image-radio" type="radio" name="primary_image" id="existing_image_{{ $image->id }}" value="{{ $image->id }}" {{ $image->is_primary ? 'checked' : '' }}>
                                                <label class="form-check-label" for="existing_image_{{ $image->id }}">
                                                    Primary
                                                </label>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-image-btn" data-image-id="{{ $image->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Hidden inputs for image deletion -->
                    <div id="delete-images-container"></div>
                </div>
                
                <!-- Add New Images Section -->
                <div class="mb-3">
                    <label class="form-label">Add New Images</label>
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
                    <div class="form-text">
                        Upload up to {{ max(0, 4 - $facilityItem->images->count()) }} more images (JPEG, PNG, JPG, GIF, max 2MB each)
                    </div>
                    
                    <div id="image-preview-container" class="row mt-3"></div>
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Item
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
        
        // Apply initial filtering based on selected category
        if(categoryFilter.value) {
            filterFacilities(categoryFilter.value);
        }
        
        categoryFilter.addEventListener('change', function() {
            filterFacilities(this.value);
        });
        
        function filterFacilities(categoryId) {
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
                if (option.value === '' || !categoryId || option.dataset.category === categoryId) {
                    facilitySelect.add(option.cloneNode(true));
                }
            });
        }
        
        // Track deleted images
        const deleteImagesContainer = document.getElementById('delete-images-container');
        const deleteButtons = document.querySelectorAll('.delete-image-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                const container = document.getElementById(`image-container-${imageId}`);
                
                // Create hidden input for the deleted image
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'delete_images[]';
                hiddenInput.value = imageId;
                deleteImagesContainer.appendChild(hiddenInput);
                
                // Hide the image container
                container.style.display = 'none';
                
                // If the deleted image was primary, select another image as primary if available
                const deletedRadio = document.getElementById(`existing_image_${imageId}`);
                if (deletedRadio.checked) {
                    const visibleRadios = Array.from(document.querySelectorAll('.existing-image-radio')).filter(radio => {
                        const radioContainer = document.getElementById(`image-container-${radio.value}`);
                        return radioContainer.style.display !== 'none';
                    });
                    
                    if (visibleRadios.length > 0) {
                        visibleRadios[0].checked = true;
                    }
                }
            });
        });
        
        // Update primary image selection
        const existingImageRadios = document.querySelectorAll('.existing-image-radio');
        existingImageRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('#current-images-container .card').forEach(card => {
                    card.classList.remove('border-primary');
                });
                
                if (this.checked) {
                    this.closest('.card').classList.add('border-primary');
                }
            });
        });
        
        // New image preview functionality
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        const maxAdditionalImages = {{ max(0, 4 - $facilityItem->images->count()) }};
        
        imageInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const imageFiles = Array.from(this.files).slice(0, maxAdditionalImages);
            
            if (imageFiles.length > 0) {
                imageFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6 mb-3';
                    
                    reader.onload = function(e) {
                        col.innerHTML = `
                            <div class="card h-100">
                                <img src="${e.target.result}" class="card-img-top" alt="Preview" style="height: 120px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <p class="card-text small mb-0">New Image ${index + 1}</p>
                                </div>
                            </div>
                        `;
                        
                        previewContainer.appendChild(col);
                    }
                    
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
@endpush