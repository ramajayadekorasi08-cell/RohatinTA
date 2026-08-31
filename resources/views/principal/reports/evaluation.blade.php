@extends('layouts.app')

@section('title', 'Laporan Evaluasi')
@section('page-title', 'Laporan Evaluasi')

@section('breadcrumb')
    <a href="{{ route('principal.dashboard') }}">Kepala Sekolah</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Laporan Evaluasi</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('principal.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    <div class="nav-label">Monitoring</div>
    <a href="{{ route('principal.ahp.index') }}" class="nav-link">
        <i class="bi bi-bar-chart-line"></i>
        <span>Hasil Pengaduan</span>
    </a>
    <div class="nav-label">Laporan</div>
    <a href="{{ route('principal.reports.complaints') }}" class="nav-link">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan Pengaduan</span>
    </a>
    <a href="{{ route('principal.reports.evaluation') }}" class="nav-link active">
        <i class="bi bi-star"></i>
        <span>Laporan Evaluasi</span>
    </a>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-5">
            <div class="stat-card h-100 d-flex flex-column justify-content-center align-items-center mb-0 text-center py-5">
                <h6 class="text-muted mb-2 fw-bold">Skor Rata-Rata Kepuasan Layanan</h6>
                <div class="display-3 fw-bold" style="color: #f59e0b;">{{ number_format($avgRating, 1) }}</div>
                <div class="d-flex text-warning fs-3 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            <i class="bi bi-star-fill me-1"></i>
                        @else
                            <i class="bi bi-star me-1"></i>
                        @endif
                    @endfor
                </div>
                <p class="small text-muted mt-3 mb-0">Berdasarkan {{ $evaluations->total() }} ulasan masuk.</p>
            </div>
        </div>
        <div class="col-md-7">
            <div class="data-card h-100">
                <div class="card-header-custom border-0">
                    <h6>Distribusi Rating</h6>
                </div>
                <div class="card-body-custom pt-0">
                    @php $totalRatings = max($evaluations->total(), 1); @endphp
                    @for($i = 5; $i >= 1; $i--)
                        @php 
                            $count = $ratingCounts[$i] ?? 0;
                            $percent = ($count / $totalRatings) * 100;
                        @endphp
                        <div class="d-flex align-items-center mb-3">
                            <div class="d-flex align-items-center text-warning" style="min-width: 65px">
                                <span class="fw-bold fs-6 text-dark me-1">{{ $i }}</span> <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="small fw-semibold text-muted" style="min-width: 40px; text-align: right">{{ $count }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and DataTable -->
    <div class="data-card mb-4">
        <div class="card-header-custom">
            <h6><i class="bi bi-funnel me-2"></i>Filter Evaluasi</h6>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('principal.reports.evaluation') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Rating</label>
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary-custom w-100"><i class="bi bi-search me-1"></i>Terapkan</button>
                    <a href="{{ route('principal.reports.evaluation') }}" class="btn btn-sm btn-light border w-100"><i class="bi bi-x-circle me-1"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="data-card">
        <div class="card-header-custom">
            <h6><i class="bi bi-chat-right-quote me-2"></i>Ulasan & Evaluasi Wali Murid</h6>
        </div>
        <div class="card-body-custom p-0">
            <div class="list-group list-group-flush">
                @forelse($evaluations as $evaluation)
                    <div class="list-group-item p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 44px; height: 44px; border-radius: 50%">
                                    {{ substr($evaluation->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $evaluation->user->name ?? 'Anonim' }}</h6>
                                    <div class="small text-muted">{{ $evaluation->created_at->format('d M Y, H:i') }} (No Tiket: {{ $evaluation->complaint->tracking_code ?? 'N/A' }})</div>
                                </div>
                            </div>
                            <div class="text-warning d-flex fs-6">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $evaluation->rating)
                                        <i class="bi bi-star-fill me-1"></i>
                                    @else
                                        <i class="bi bi-star me-1 text-light border-warning"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="alert alert-light border mt-3 mb-0 text-dark">
                            "{{ $evaluation->comment ?? 'Tidak ada pesan umpan balik.' }}"
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">Belum ada evaluasi untuk periode ini.</div>
                @endforelse
            </div>
            
            @if($evaluations->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $evaluations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
