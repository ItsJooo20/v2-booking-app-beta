@extends('layout.navbar')

@section('title', 'Edit Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Edit Fasilitas</h1>
        <a href="{{ route('facilities.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $facility->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="3">{{ old('description', $facility->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Gambar Fasilitas</label>
                    @if($facility->image_path)
                        <div class="mb-2" id="current-image">
                            <img src="{{ $facility->getImageUrl() }}"
                                 alt="{{ $facility->name }}"
                                 class="img-thumbnail"
                                 style="max-height:200px;">
                            <div class="form-text">Gambar saat ini</div>
                        </div>
                    @endif
                    <input type="file"
                           class="form-control @error('image') is-invalid @enderror"
                           id="image"
                           name="image"
                           accept="image/*">
                    <div class="form-text">Unggah gambar baru (JPEG, PNG, JPG, GIF, maks 2MB)</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="image-preview" class="mt-2"></div>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror"
                            id="category_id"
                            name="category_id"
                            required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $facility->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input @error('can_be_addon') is-invalid @enderror"
                                   type="checkbox"
                                   id="can_be_addon"
                                   name="can_be_addon"
                                   value="1"
                                   {{ old('can_be_addon', $facility->can_be_addon) ? 'checked' : '' }}>
                            <label class="form-check-label" for="can_be_addon">
                                Dapat menjadi Add-on
                            </label>
                            <div class="form-text">Fasilitas ini bisa ditambahkan ke fasilitas lain.</div>
                            @error('can_be_addon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input @error('can_have_addon') is-invalid @enderror"
                                   type="checkbox"
                                   id="can_have_addon"
                                   name="can_have_addon"
                                   value="1"
                                   {{ old('can_have_addon', $facility->can_have_addon) ? 'checked' : '' }}>
                            <label class="form-check-label" for="can_have_addon">
                                Dapat memiliki Add-on
                            </label>
                            <div class="form-text">Fasilitas ini dapat mempunyai fasilitas tambahan.</div>
                            @error('can_have_addon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Perbarui Fasilitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function() {
    if (!this.files.length) return;
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('image-preview').innerHTML = `
            <img src="${e.target.result}" class="img-thumbnail" style="max-height:200px;">
            <div class="form-text">Preview gambar baru</div>
        `;
        const current = document.getElementById('current-image');
        if (current) current.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush