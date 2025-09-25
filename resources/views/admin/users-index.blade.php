@extends('layout.navbar')

@section('title', 'User Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">User Management</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add New User
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="error" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="GET" class="row align-items-end g-3 mb-4">
                <div class="col-md">
                    <label for="search" class="form-label">Search by name</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="e.g. John Doe" value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <label for="role" class="form-label">Filter by role</label>
                    <select name="role" id="role" class="form-select w-auto">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>Technician</option>
                        <option value="headmaster" {{ request('role') == 'headmaster' ? 'selected' : '' }}>Headmaster</option>
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'technician' ? 'info' : ($user->role == 'headmaster' ? 'warning' : 'success')) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->phone ?: 'N/A' }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @if($user->is_active)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate {{ $user->name }}?')">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-outline-success">
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
                                <i class="bi bi-person-x mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No users found</p>
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
    
    /* Target specifically the pagination container */
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
    
    .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .card:hover .card-img-top {
        transform: scale(1.03);
    }
</style>
@endpush
