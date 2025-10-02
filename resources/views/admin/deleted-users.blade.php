@extends('layout.navbar')

@section('title', 'Pengguna Nonaktif')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="h3 mb-0">Pengguna Nonaktif</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('users.list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-people me-1"></i> Semua Pengguna
            </a>
            {{-- Aktifkan jika mau langsung ke daftar tambah --}}
            {{-- <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
            </a> --}}
        </div>
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
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="misal: Andi"
                           value="{{ request('search') }}">
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
                            <th>Terkunci Sejak</th>
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
                                            'user' => 'Pengguna'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'technician' ? 'info' : ($user->role == 'headmaster' ? 'warning' : 'success')) }}">
                                        {{ $roleMap[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->phone ?: '—' }}</td>
                                <td class="small text-muted">
                                    @if($user->updated_at)
                                        {{ $user->updated_at->locale('id')->diffForHumans() }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Pulihkan akses untuk {{ $user->name }}?')"
                                                title="Pulihkan">
                                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                        </button>
                                    </form>

                                    {{-- (Opsional) Hapus permanen
                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus permanen pengguna {{ $user->name }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-person-dash mb-2" style="font-size:2rem;"></i>
                                    <p class="text-muted mb-0">Tidak ada pengguna nonaktif</p>
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
    .page-link svg {
        width: 20px !important;
        height: 20px !important;
    }
    .page-link {
        line-height: 1;
        padding: 0.5rem !important;
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
    }
    .pagination .page-item .page-link {
        width: 38px;
        height: 38px;
    }
</style>
@endpush