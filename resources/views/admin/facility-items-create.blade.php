@extends('layout.navbar')

@section('title', 'Tambah Item Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Tambah Item Fasilitas</h1>
        <a href="{{ route('facility-items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('facility-items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label for="item_code" class="form-label">Nama Item <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('item_code') is-invalid @enderror"
                                   id="item_code"
                                   name="item_code"
                                   value="{{ old('item_code') }}"
                                   required>
                            @error('item_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter Kategori</label>
                            <select class="form-select" id="category_filter">
                                <option value="">Semua Kategori</option>
                                @foreach($facilities->pluck('category')->unique('id') as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih kategori untuk memfilter fasilitas.</div>
                        </div>

                        <div class="mb-3">
                            <label for="facility_id" class="form-label">Fasilitas <span class="text-danger">*</span></label>
                            <select class="form-select @error('facility_id') is-invalid @enderror"
                                    id="facility_id"
                                    name="facility_id"
                                    required>
                                <option value="">Pilih fasilitas</option>
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}"
                                            data-category="{{ $facility->category->id }}"
                                            {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                                        {{ $facility->name }} ({{ $facility->category->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Nomor Seri(optional)</label>
                            <input type="text"
                                   class="form-control @error('serial_number') is-invalid @enderror"
                                   id="serial_number"
                                   name="serial_number"
                                   value="{{ old('serial_number') }}">
                            @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Deskripsi/Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="5">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar (maks 4)</label>
                    <div class="input-group mb-3">
                        <input type="file"
                               class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                               id="images"
                               name="images[]"
                               accept="image/*"
                               multiple>
                        <label class="input-group-text" for="images">Pilih</label>
                        @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @error('images.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-text">Format: JPEG, PNG, JPG, GIF. Maks 2MB tiap file. Maksimal 4 gambar.</div>
                    <div id="image-preview-container" class="row mt-3"></div>
                    <input type="hidden" name="primary_image" id="primary_image" value="0">
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Item
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const categoryFilter = document.getElementById('category_filter');
    const facilitySelect = document.getElementById('facility_id');
    const originalOptions = Array.from(facilitySelect.options);

    categoryFilter.addEventListener('change', function() {
        const catId = this.value;
        while (facilitySelect.options.length) facilitySelect.remove(0);
        const placeholder = new Option('Pilih fasilitas', '');
        facilitySelect.add(placeholder);
        originalOptions.forEach(opt => {
            if (opt.value === '' ) return;
            if (!catId || opt.dataset.category === catId) {
                facilitySelect.add(opt.cloneNode(true));
            }
        });
    });

    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const primaryImageInput = document.getElementById('primary_image');

    imageInput.addEventListener('change', function(){
        previewContainer.innerHTML = '';
        const files = Array.from(this.files).slice(0,4);
        files.forEach((file, idx) => {
            const reader = new FileReader();
            const col = document.createElement('div');
            col.className = 'col-md-3 col-6 mb-3';
            reader.onload = e => {
                col.innerHTML = `
                    <div class="card h-100 ${idx===0?'border-primary':''}">
                        <img src="${e.target.result}" class="card-img-top" style="height:120px;object-fit:cover;">
                        <div class="card-body p-2">
                            <div class="form-check">
                                <input class="form-check-input primary-image-radio" type="radio" name="primary_image_radio" id="primary_image_${idx}" value="${idx}" ${idx===0?'checked':''}>
                                <label class="form-check-label small" for="primary_image_${idx}">Gambar Utama</label>
                            </div>
                        </div>
                    </div>`;
                previewContainer.appendChild(col);
                col.querySelector('.primary-image-radio').addEventListener('change', function(){
                    primaryImageInput.value = this.value;
                    previewContainer.querySelectorAll('.card').forEach(c => c.classList.remove('border-primary'));
                    this.closest('.card').classList.add('border-primary');
                });
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush