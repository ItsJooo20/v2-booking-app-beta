@extends('layout.navbar-calendar')

@section('title', 'Pemesanan Fasilitas')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet" />
<style>
    .fc .fc-button-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }
    .fc .fc-button-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    .fc-event {
        border-radius: 6px;
        padding: 2px 4px;
        border: none;
    }
    #bookingCalendar { min-height: 600px; }

    .badge.bg-purple { background:#9C27B0 !important; color:#fff !important; }
    .badge.bg-orange { background:#FF9800 !important; }

    .badge {
        display:inline-block!important; padding:.35em .65em!important;
        font-size:.75em!important; font-weight:700!important; line-height:1!important;
        border-radius:.25rem!important;
    }
    .action-dot {
        display:inline-block; width:8px; height:8px; background:#dc3545;
        border-radius:50%; margin-right:6px;
    }
    .booking-card { transition:transform .2s; }
    .booking-card:hover { transform:translateY(-5px); }

    .nav-tabs { border-bottom:1px solid #dee2e6; margin-bottom:20px; }
    .nav-tabs .nav-link {
        border:1px solid transparent; font-weight:500;
        padding:.75rem 1.25rem; margin-bottom:-1px;
    }
    .nav-tabs .nav-link.active {
        color:var(--primary-color);
        background:#fff;
        border-color:#dee2e6 #dee2e6 #fff;
        border-bottom:3px solid var(--primary-color);
    }
    .status-count {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:18px; height:18px; padding:0 5px;
        font-size:11px; font-weight:600;
        background:rgba(0,0,0,.1); border-radius:10px; margin-left:4px;
    }

    .custom-pagination .page-link {
        display:flex; align-items:center; justify-content:center;
        height:36px; min-width:36px; padding:0 10px; font-size:.875rem;
    }
    .custom-pagination .page-item.active .page-link {
        background:var(--primary-color); border-color:var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Pemesanan Fasilitas</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Pemesanan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    @php
        $pendingCount = $upcomingBookings->where('status','pending')->count();
        $returnSubmitted = \App\Models\Booking::where('status','return submitted')->get();
        $returnCount = $returnSubmitted->count();
        $actionCount = $pendingCount + $returnCount;

        // Helper tampilan status
        function statusLabel($s) {
            return match($s) {
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                'needs return' => 'Perlu Pengembalian',
                'return submitted' => 'Pengembalian Diajukan',
                default => ucfirst($s),
            };
        }
    @endphp

    <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="action-tab" data-bs-toggle="tab"
                data-bs-target="#action-pane" type="button" role="tab" aria-controls="action-pane" aria-selected="true">
                Perlu Tindakan
                <span class="status-count">{{ $actionCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab"
                data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="false">
                Menunggu
                <span class="status-count">{{ $pendingCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="return-tab" data-bs-toggle="tab"
                data-bs-target="#return-pane" type="button" role="tab" aria-controls="return-pane" aria-selected="false">
                Pengembalian Diajukan
                <span class="status-count">{{ $returnCount }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="bookingTabsContent">
        {{-- Tab Perlu Tindakan --}}
        <div class="tab-pane fade show active" id="action-pane" role="tabpanel" aria-labelledby="action-tab">
            <div class="row">
                @php
                    $actionBookings = $upcomingBookings->where('status','pending')->merge($returnSubmitted);
                @endphp
                @forelse($actionBookings as $booking)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm booking-card">
                            <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <span class="action-dot" title="Perlu tindakan"></span>
                                    {{ $booking->facilityItem->item_code ?? 'Item' }}
                                </h5>
                                <span class="badge
                                    @if($booking->status === 'pending') bg-warning text-dark
                                    @elseif($booking->status === 'return submitted') bg-purple
                                    @endif">
                                    {{ statusLabel($booking->status) }}
                                </span>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-2 small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($booking->start_datetime)
                                        @php
                                            $s = $booking->start_datetime instanceof \Carbon\Carbon ? $booking->start_datetime : \Carbon\Carbon::parse($booking->start_datetime);
                                            $e = $booking->end_datetime instanceof \Carbon\Carbon ? $booking->end_datetime : \Carbon\Carbon::parse($booking->end_datetime);
                                        @endphp
                                        {{ $s->format('d M Y H:i') }} - {{ $e->format('d M Y H:i') }}
                                    @else
                                        Waktu tidak tersedia
                                    @endif
                                </div>
                                <p class="card-text mb-2">
                                    {{ $booking->purpose ? \Illuminate\Support\Str::limit($booking->purpose, 100) : 'Tidak ada keterangan tujuan' }}
                                </p>
                                <div class="d-flex align-items-center text-muted small mb-3">
                                    <i class="bi bi-person me-1"></i>
                                    {{ $booking->user->name ?? 'Pengguna' }}
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('bookings.show',$booking->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail & Tindakan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size:3rem;"></i>
                        <h5 class="mt-3 text-muted">Tidak ada yang memerlukan tindakan</h5>
                        <p class="text-muted">Semua pemesanan telah diproses</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab Menunggu --}}
        <div class="tab-pane fade" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab">
            <div class="row">
                @php $pendingBookings = $upcomingBookings->where('status','pending'); @endphp
                @forelse($pendingBookings as $booking)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm booking-card">
                            <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between">
                                <h5 class="mb-0">
                                    <span class="action-dot" title="Perlu tindakan"></span>
                                    {{ $booking->facilityItem->item_code }}
                                </h5>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-2 small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @php
                                        $s = $booking->start_datetime instanceof \Carbon\Carbon ? $booking->start_datetime : \Carbon\Carbon::parse($booking->start_datetime);
                                        $e = $booking->end_datetime instanceof \Carbon\Carbon ? $booking->end_datetime : \Carbon\Carbon::parse($booking->end_datetime);
                                    @endphp
                                    {{ $s->format('d M Y H:i') }} - {{ $e->format('d M Y H:i') }}
                                </div>
                                <p class="card-text mb-2">{{ \Illuminate\Support\Str::limit($booking->purpose, 100) }}</p>
                                <div class="d-flex align-items-center text-muted small mb-3">
                                    <i class="bi bi-person me-1"></i>{{ $booking->user->name }}
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('bookings.show',$booking->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail & Tindakan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size:3rem;"></i>
                        <h5 class="mt-3 text-muted">Tidak ada booking menunggu</h5>
                        <p class="text-muted">Semua permintaan telah diproses</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab Pengembalian Diajukan --}}
        <div class="tab-pane fade" id="return-pane" role="tabpanel" aria-labelledby="return-tab">
            <div class="row">
                @forelse($returnSubmitted as $booking)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm booking-card">
                            <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between">
                                <h5 class="mb-0">
                                    <span class="action-dot"></span>
                                    {{ $booking->facilityItem->item_code ?? 'Item' }}
                                </h5>
                                <span class="badge bg-purple">Pengembalian Diajukan</span>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-2 small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @php
                                        $s = $booking->start_datetime instanceof \Carbon\Carbon ? $booking->start_datetime : \Carbon\Carbon::parse($booking->start_datetime);
                                        $e = $booking->end_datetime instanceof \Carbon\Carbon ? $booking->end_datetime : \Carbon\Carbon::parse($booking->end_datetime);
                                    @endphp
                                    {{ $s->format('d M Y H:i') }} - {{ $e->format('d M Y H:i') }}
                                </div>
                                <p class="card-text mb-2">{{ $booking->purpose ? \Illuminate\Support\Str::limit($booking->purpose,100) : 'Tidak ada keterangan tujuan' }}</p>
                                <div class="d-flex align-items-center text-muted small mb-3">
                                    <i class="bi bi-person me-1"></i>{{ $booking->user->name ?? 'Pengguna' }}
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('bookings.show',$booking->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail & Verifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-check-circle text-muted" style="font-size:3rem;"></i>
                        <h5 class="mt-3 text-muted">Tidak ada pengembalian menunggu verifikasi</h5>
                        <p class="text-muted">Semua pengembalian sudah diverifikasi</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pagination manual (jika tetap diperlukan; kalau pakai default laravel bisa dihapus) --}}
    @if(isset($upcomingBookings) && method_exists($upcomingBookings,'hasPages') && $upcomingBookings->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            <nav aria-label="Navigasi halaman">
                <ul class="pagination custom-pagination">
                    @if($upcomingBookings->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $upcomingBookings->previousPageUrl() }}" rel="prev">&laquo;</a>
                        </li>
                    @endif

                    @php
                        $currentPage = $upcomingBookings->currentPage();
                        $lastPage = $upcomingBookings->lastPage();
                        $window = 1;
                    @endphp

                    @if($currentPage > ($window + 2))
                        <li class="page-item"><a class="page-link" href="{{ $upcomingBookings->url(1) }}">1</a></li>
                        @if($currentPage > ($window + 3))
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif
                    @endif

                    @for($i = max(1, $currentPage - $window); $i <= min($lastPage, $currentPage + $window); $i++)
                        <li class="page-item {{ $i==$currentPage?'active':'' }}">
                            <a class="page-link" href="{{ $upcomingBookings->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if($currentPage < ($lastPage - $window - 1))
                        @if($currentPage < ($lastPage - $window - 2))
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $upcomingBookings->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if($upcomingBookings->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $upcomingBookings->nextPageUrl() }}" rel="next">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

    {{-- Kalender --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> Tampilan Kalender</h5>
        </div>
        <div class="card-body">
            <div id="bookingCalendar"></div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-2">
            <h6 class="mb-0">Legenda Status</h6>
        </div>
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-3 justify-content-center small">
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#FBBC05;"></span>Menunggu</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#34A853;"></span>Disetujui</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#EA4335;"></span>Ditolak</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#1A73E8;"></span>Selesai</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#5F6368;"></span>Dibatalkan</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#FF9800;"></span>Perlu Pengembalian</div>
                <div class="d-flex align-items-center"><span class="rounded-circle me-2" style="width:12px;height:12px;background:#9C27B0;"></span>Pengembalian Diajukan</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('bookingCalendar');
    if (!el) return;
    const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: @json($calendarBookings),
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: 'short' },
        height: 'auto',
        themeSystem: 'bootstrap5',
        nowIndicator: true,
        navLinks: true,
        dayMaxEvents: true,
    });
    calendar.render();
});
</script>
@endsection