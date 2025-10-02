@extends('layout.navbar')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manajemen Pengguna</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="GET" class="row align-items-end g-3 mb-4">
                <div class="col-md">
                    <label for="search" class="form-label">Cari berdasarkan nama</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="misal: Budi Setiawan" value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <label for="role" class="form-label">Filter per peran</label>
                    <select name="role" id="role" class="form-select w-auto">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-auto">
                    <a href="{{ route('users.list') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleMap = [
                                        'admin' => 'Admin',
                                        'technician' => 'Teknisi',
                                        'headmaster' => 'Kepala Sekolah',
                                        'user' => 'User'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'technician' ? 'info' : ($user->role == 'headmaster' ? 'warning' : 'success')) }}">
                                    {{ $roleMap[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    {{-- Edit jika perlu --}}
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->is_active)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menonaktifkan {{ $user->name }}?')">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Nonaktifkan">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Pulihkan">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-person-x mb-2" style="font-size:2rem;"></i>
                                <p class="text-muted mb-0">Tidak ada pengguna</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

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