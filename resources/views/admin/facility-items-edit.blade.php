@extends('layout.navbar')

@section('title', 'Edit Item Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Edit Item Fasilitas</h1>
        <a href="{{ route('facility-items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
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
                            <label for="item_code" class="form-label">Nama Item <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('item_code') is-invalid @enderror"
                                   id="item_code"
                                   name="item_code"
                                   value="{{ old('item_code', $facilityItem->item_code) }}"
                                   required>
                            @error('item_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter Kategori</label>
                            <select class="form-select" id="category_filter">
                                <option value="">Semua Kategori</option>
                                @foreach($facilities->pluck('category')->unique('id') as $cat)
                                    <option value="{{ $cat->id }}" {{ $facilityItem->facility->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
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
                                            {{ old('facility_id', $facilityItem->facility_id) == $facility->id ? 'selected' : '' }}>
                                        {{ $facility->name }} ({{ $facility->category->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Nomor Seri(opsional)</label>
                            <input type="text"
                                   class="form-control @error('serial_number') is-invalid @enderror"
                                   id="serial_number"
                                   name="serial_number"
                                   value="{{ old('serial_number', $facilityItem->serial_number) }}">
                            @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Deskripsi/Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="7">{{ old('notes', $facilityItem->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                <!-- Gambar Saat Ini -->
                <div class="mb-4">
                    <label class="form-label">Gambar Saat Ini</label>
                    <div class="row" id="current-images-container">
                        @foreach($facilityItem->images as $image)
                            <div class="col-md-3 col-6 mb-3" id="image-container-{{ $image->id }}">
                                <div class="card h-100 {{ $image->is_primary ? 'border-primary' : '' }}">
                                    <img src="{{ $image->getImageUrl() }}" class="card-img-top" style="height:120px;object-fit:cover;">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between">
                                            <div class="form-check">
                                                <input class="form-check-input existing-image-radio"
                                                       type="radio"
                                                       name="primary_image"
                                                       id="existing_image_{{ $image->id }}"
                                                       value="{{ $image->id }}"
                                                       {{ $image->is_primary ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="existing_image_{{ $image->id }}">
                                                    Utama
                                                </label>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-image-btn"
                                                    data-image-id="{{ $image->id }}"
                                                    title="Hapus Gambar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="delete-images-container"></div>
                </div>

                <!-- Tambah Gambar Baru -->
                <div class="mb-3">
                    <label class="form-label">Tambah Gambar Baru</label>
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
                    <div class="form-text">
                        Maks tambah {{ max(0, 4 - $facilityItem->images->count()) }} gambar lagi (JPEG, PNG, JPG, GIF, maks 2MB per file)
                    </div>
                    <div id="image-preview-container" class="row mt-3"></div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Perbarui Item
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
    // Filter fasilitas berdasarkan kategori
    const categoryFilter = document.getElementById('category_filter');
    const facilitySelect = document.getElementById('facility_id');
    const originalOptions = Array.from(facilitySelect.options);

    function rebuildFacilities(catId) {
        while (facilitySelect.options.length) facilitySelect.remove(0);
        facilitySelect.add(new Option('Pilih fasilitas',''));
        originalOptions.forEach(opt => {
            if (opt.value === '') return;
            if (!catId || opt.dataset.category === catId) {
                facilitySelect.add(opt.cloneNode(true));
            }
        });
    }
    categoryFilter.addEventListener('change', function(){ rebuildFacilities(this.value); });

    // Hapus gambar lama
    const deleteImagesContainer = document.getElementById('delete-images-container');
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.imageId;
            const wrapper = document.getElementById('image-container-'+id);
            wrapper.style.display='none';
            const hidden = document.createElement('input');
            hidden.type='hidden';
            hidden.name='delete_images[]';
            hidden.value=id;
            deleteImagesContainer.appendChild(hidden);
            const radio = document.getElementById('existing_image_'+id);
            if (radio && radio.checked) {
                const remaining = Array
                    .from(document.querySelectorAll('.existing-image-radio'))
                    .filter(r => document.getElementById('image-container-'+r.value).style.display !== 'none');
                if (remaining.length) {
                    remaining[0].checked = true;
                    remaining[0].closest('.card').classList.add('border-primary');
                }
            }
        });
    });

    // Highlight primary saat ganti
    document.querySelectorAll('.existing-image-radio').forEach(radio => {
        radio.addEventListener('change', function(){
            document.querySelectorAll('#current-images-container .card').forEach(c => c.classList.remove('border-primary'));
            if (this.checked) this.closest('.card').classList.add('border-primary');
        });
    });

    // Preview gambar baru
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const maxAdd = {{ max(0, 4 - $facilityItem->images->count()) }};
    imageInput.addEventListener('change', function(){
        previewContainer.innerHTML='';
        const files = Array.from(this.files).slice(0,maxAdd);
        files.forEach((file,i)=>{
            const reader = new FileReader();
            const col = document.createElement('div');
            col.className='col-md-3 col-6 mb-3';
            reader.onload = e => {
                col.innerHTML = `
                    <div class="card h-100">
                        <img src="${e.target.result}" class="card-img-top" style="height:120px;object-fit:cover;">
                        <div class="card-body p-2">
                            <p class="card-text small mb-0">Gambar Baru ${i+1}</p>
                        </div>
                    </div>`;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush