@extends('layouts.app')

@section('title', 'Laporan Pengaduan')
@section('page-title', 'Laporan Pengaduan')

@section('breadcrumb')
    <a href="{{ route('principal.dashboard') }}">Kepala Sekolah</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Laporan Pengaduan</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('principal.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Laporan</div>
    <a href="{{ route('principal.reports.complaints') }}" class="nav-link active">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan Pengaduan</span>
    </a>
    <a href="{{ route('principal.reports.evaluation') }}" class="nav-link">
        <i class="bi bi-star"></i>
        <span>Laporan Evaluasi</span>
    </a>
@endsection

@section('content')
    <div class="data-card mb-4">
        <div class="card-header-custom">
            <h6><i class="bi bi-funnel me-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('principal.reports.complaints') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Bulan/Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="on_progress" {{ request('status') == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary-custom w-100"><i class="bi bi-search me-1"></i>Terapkan</button>
                    <a href="{{ route('principal.reports.complaints') }}" class="btn btn-sm btn-light border w-100"><i class="bi bi-x-circle me-1"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="data-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-file-text me-2"></i>Data Laporan Pengaduan</h6>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-none d-md-inline-block">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No Tiket / Tanggal</th>
                            <th>Pengirim (Wali Murid)</th>
                            <th>Siswa / Kategori</th>
                            <th>Status & Prioritas</th>
                            <th>Waktu Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color: var(--primary)">{{ $complaint->tracking_code }}</div>
                                    <div class="small text-muted">{{ $complaint->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>{{ $complaint->parentUser->name ?? '-' }}</td>
                                <td>
                                    <div class="fw-medium">{{ $complaint->student->name ?? '-' }}</div>
                                    <div class="small text-muted">{{ $complaint->category->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                            {{ $complaint->statusLabel }}
                                        </span>
                                        @if($complaint->priority_level)
                                            <span class="badge-status badge-priority-{{ $complaint->priority_level }}">
                                                {{ ucfirst($complaint->priority_level) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($complaint->status === 'resolved' && $complaint->resolved_at)
                                        <div class="small text-success fw-medium">Selesai: {{ $complaint->resolved_at->format('d/m/Y') }}</div>
                                    @else
                                        <div class="small text-muted">Belum Selesai</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Tidak ada data laporan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($complaints->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
