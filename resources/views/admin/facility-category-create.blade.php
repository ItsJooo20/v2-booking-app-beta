@extends('layout.navbar')

@section('title', 'Buat Kategori Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Buat Kategori Fasilitas</h1>
        <a href="{{ route('facility-categories.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facility-categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
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
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Gambar Kategori</label>
                    <input type="file"
                           class="form-control @error('image') is-invalid @enderror"
                           id="image"
                           name="image"
                           accept="image/*">
                    <div class="form-text">
                        Unggah gambar representatif (JPEG, PNG, JPG, GIF, maks 2MB)
                    </div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="image-preview" class="mt-3"></div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input @error('requires_return') is-invalid @enderror"
                               type="checkbox"
                               id="requires_return"
                               name="requires_return"
                               value="1"
                               {{ old('requires_return') ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_return">
                            Perlu Dikembalikan
                        </label>
                        <div class="form-text">
                            Centang jika item dalam kategori ini harus dikembalikan setelah dipakai.
                        </div>
                        @error('requires_return')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    if (!this.files.length) return;
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(ev) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = `
            <img src="${ev.target.result}" class="img-thumbnail" style="max-height:200px;">
        `;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush