@extends('layout.navbar')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="container-fluid py-4">
    <!-- Header dengan tombol kembali -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Booking #{{ $booking->id }}</h1>
            <div class="text-muted small">
                <a href="{{ route('bookings.index') }}" class="text-decoration-none">Bookings</a>
                <i class="bi bi-chevron-right mx-1 small"></i>
                <span>Details</span>
            </div>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success') || session('error'))
        <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} mb-4">
            <i class="bi bi-{{ session('error') ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Kolom Utama -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Header Info Booking -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Informasi Booking</h5>
                        <span class="badge 
                            @if($booking->status === 'approved') bg-success
                            @elseif($booking->status === 'pending') bg-warning text-dark
                            @elseif($booking->status === 'rejected') bg-danger
                            @elseif($booking->status === 'completed') bg-primary
                            @elseif($booking->status === 'cancelled') bg-secondary
                            @elseif($booking->status === 'needs return') bg-warning
                            @elseif($booking->status === 'return submitted') bg-info
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>

                    <!-- Informasi Dasar -->
                    <div class="row">
                        <div class="col-md-6">
                            {{-- <div class="mb-3">
                                <div class="text-muted small">Fasilitas</div>
                                <div class="fw-medium">{{ $booking->facilityItem->facility->name }}</div>
                            </div> --}}
                            <div class="mb-3">
                                <div class="text-muted small">Nama Item</div>
                                <div class="fw-medium">{{ $booking->facilityItem->item_code }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Mulai</div>
                                <div class="fw-medium">
                                    @if($booking->start_datetime instanceof \Carbon\Carbon)
                                        {{ $booking->start_datetime->format('d F, Y - H:i') }}
                                    @else
                                        {{ date('d F, Y - H:i', strtotime($booking->start_datetime)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted small">Tujuan</div>
                                <div class="fw-medium">{{ $booking->purpose }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted small">Di Booking oleh</div>
                                <div class="fw-medium">{{ $booking->user->name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Selesai</div>
                                <div class="fw-medium">
                                    @if($booking->end_datetime instanceof \Carbon\Carbon)
                                        {{ $booking->end_datetime->format('d F, Y - H:i') }}
                                    @else
                                        {{ date('d F, Y - H:i', strtotime($booking->end_datetime)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Durasi</div>
                                <div class="fw-medium">
                                    @php
                                        $start = $booking->start_datetime instanceof \Carbon\Carbon
                                            ? $booking->start_datetime
                                            : \Carbon\Carbon::parse($booking->start_datetime);
                                        $end = $booking->end_datetime instanceof \Carbon\Carbon
                                            ? $booking->end_datetime
                                            : \Carbon\Carbon::parse($booking->end_datetime);
                                        $diff = $start->diff($end);
                                        $hours = $diff->h + ($diff->days * 24);
                                        $minutes = $diff->i;
                                        echo $hours . ' jam' . ' ' . $minutes . ' menit';
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Informasi Pengembalian (bila ada) -->
                    @if($booking->equipmentReturn)
                        <hr class="my-4">
                        <div class="mb-3">
                            <h6 class="mb-3">Equipment Return</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Return Date</div>
                                        <div class="fw-medium">
                                            @if($booking->equipmentReturn->return_date instanceof \Carbon\Carbon)
                                                {{ $booking->equipmentReturn->return_date->format('d F, Y - H:i') }}
                                            @else
                                                {{ date('d F, Y - H:i', strtotime($booking->equipmentReturn->return_date)) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Condition</div>
                                        <div class="fw-medium">
                                            <span class="badge
                                                @if($booking->equipmentReturn->condition_status === 'good') bg-success
                                                @elseif($booking->equipmentReturn->condition_status === 'damaged') bg-danger
                                                @elseif($booking->equipmentReturn->condition_status === 'missing') bg-dark
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($booking->equipmentReturn->condition_status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if($booking->equipmentReturn->notes)
                                        <div class="mb-3">
                                            <div class="text-muted small">Notes</div>
                                            <div class="fw-medium">{{ $booking->equipmentReturn->notes }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Actions -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Actions</h5>

        @if(in_array(Auth::user()->role, ['admin','superadmin']) && $booking->status == 'pending')
            <div class="d-grid gap-2 mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-2"></i>Approve
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-2"></i>Reject
                </button>
            </div>
        @endif

        @if($booking->status === 'needs return' && (Auth::id()==$booking->user_id || in_array(Auth::user()->role, ['admin','superadmin'])))
            <a href="{{ route('bookings.return.show', $booking->id) }}" class="btn btn-warning w-100 mb-2">
                <i class="bi bi-box-arrow-in-down me-2"></i>Submit Return
            </a>
        @endif

        @if($booking->status === 'return submitted' && in_array(Auth::user()->role, ['admin','superadmin']))
            <a href="{{ route('bookings.return.verify.show', $booking->id) }}" class="btn btn-info w-100 mb-2">
                <i class="bi bi-check-circle me-2"></i>Verify Return
            </a>
        @endif

        @if(in_array($booking->status, ['pending','approved']) && (Auth::id()==$booking->user_id || in_array(Auth::user()->role,['admin','superadmin'])))
            <div class="d-grid gap-2">
                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-circle me-2"></i>Cancel Booking
                </button>
            </div>
        @endif
    </div>
</div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Info Pengguna -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Informasi User</h5>
                    <div class="mb-3">
                        <div class="text-muted small">Nama</div>
                        <div class="fw-medium">{{ $booking->user->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Role</div>
                        <div class="fw-medium">{{ $booking->user->role }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-medium">{{ $booking->user->email }}</div>
                    </div>
                    @if($booking->user->phone)
                        <div class="mb-0">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-medium">{{ $booking->user->phone }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Timeline</h5>

                    <div class="mb-3">
                        <div class="fw-medium">
                            @if($booking->created_at instanceof \Carbon\Carbon)
                                {{ $booking->created_at->format('d F, Y - H:i') }}
                            @else
                                {{ date('d F, Y - H:i', strtotime($booking->created_at)) }}
                            @endif
                        </div>
                        <div class="small">Booking dibuat oleh {{ $booking->user->name }}</div>
                    </div>

                    @if($booking->status != 'pending')
                        <div class="mb-3">
                            <div class="fw-medium">
                                @if($booking->updated_at instanceof \Carbon\Carbon)
                                    {{ $booking->updated_at->format('d F, Y - H:i') }}
                                @else
                                    {{ date('d F, Y - H:i', strtotime($booking->updated_at)) }}
                                @endif
                            </div>
                            <div class="small">Status changed to {{ ucfirst(str_replace('_',' ',$booking->status)) }}</div>
                        </div>
                    @endif

                    @if($booking->equipmentReturn)
                        <div class="mb-3">
                            <div class="fw-medium">
                                @if($booking->equipmentReturn->created_at instanceof \Carbon\Carbon)
                                    {{ $booking->equipmentReturn->created_at->format('d F, Y - H:i') }}
                                @else
                                    {{ date('d F, Y - H:i', strtotime($booking->equipmentReturn->created_at)) }}
                                @endif
                            </div>
                            <div class="small">Equipment return submitted</div>
                        </div>

                        @if($booking->equipmentReturn->verified_at)
                            <div class="mb-0">
                                <div class="fw-medium">
                                    @if($booking->equipmentReturn->verified_at instanceof \Carbon\Carbon)
                                        {{ $booking->equipmentReturn->verified_at->format('d F, Y - H:i') }}
                                    @else
                                        {{ date('d F, Y - H:i', strtotime($booking->equipmentReturn->verified_at)) }}
                                    @endif
                                </div>
                                <div class="small">Return verified as {{ $booking->equipmentReturn->condition_status }}</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            
        </div>
    </div>

    <!-- Modals -->
    <!-- Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin Approve peminjaman untuk <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p class="mb-0">Menyetujui peminjaman akan membatalkan peminjaman fasilitas yang sama di hari dan jam yang sama.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('bookings.approve', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pemesanan ini untuk <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p class="mb-0">Tindakan ini akan memberi tahu pengguna bahwa permintaan pemesanan mereka telah ditolak.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('bookings.reject', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Reject Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Cancel -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pemesanan ini untuk <strong>{{ $booking->facilityItem->item_code }}</strong>?</p>
                    <p class="mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tidak Jadi</button>
                    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Batalkan Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection