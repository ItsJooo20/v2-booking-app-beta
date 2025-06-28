@extends('layout.navbar')

@section('title', 'Edit Facility Category')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Facility Category</h1>
        <a href="{{ route('facility-categories.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Categories
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facility-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Category Image</label>
                    @if($category->image_path)
                        <div class="mb-2" id="current-image">
                            <img src="{{ $category->getImageUrl() }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-height: 200px;">
                            <div class="form-text">Current image</div>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                    <div class="form-text">Upload a new image to replace the current one (JPEG, PNG, JPG, GIF, max 2MB)</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input @error('requires_return') is-invalid @enderror" type="checkbox" id="requires_return" name="requires_return" value="1" {{ old('requires_return', $category->requires_return) ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_return">
                            Requires Return
                        </label>
                        <div class="form-text">Check if items in this category need to be returned after use</div>
                    </div>
                    @error('requires_return')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('image').onchange = function(e) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.querySelector('#image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.id = 'image-preview';
                preview.classList.add('mt-2');
                document.querySelector('#image').parentNode.appendChild(preview);
            }
            preview.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                <div class="form-text">New image preview</div>
            `;
            
            const currentImage = document.querySelector('#current-image');
            if (currentImage) {
                currentImage.style.display = 'none';
            }
        }
        reader.readAsDataURL(this.files[0]);
    };
</script>
@endpush