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
    <div class="nav-label">Monitoring</div>
    <a href="{{ route('principal.ahp.index') }}" class="nav-link">
        <i class="bi bi-bar-chart-line"></i>
        <span>Hasil Pengaduan</span>
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
    @push('styles')
    <style>
        /* Print Styles */
        @media print {
            body { background: #fff !important; color: #000 !important; }
            .sidebar, .top-navbar, .filter-card, .btn-print, .pagination, .toast-container { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            .content-area { padding: 0 !important; }
            .data-card { border: none !important; box-shadow: none !important; background: transparent !important; }
            .card-header-custom { display: none !important; }
            .table-modern { width: 100% !important; margin-bottom: 1rem; color: #000; }
            .table-modern th, .table-modern td { border: 1px solid #000 !important; padding: 8px !important; }
            .table-modern thead th { background: #f0f0f0 !important; font-weight: bold; text-align: center; color: #000 !important; border-bottom: 2px solid #000 !important; }
            .badge-status { border: none !important; background: transparent !important; color: #000 !important; padding: 0 !important; font-weight: normal !important; text-transform: capitalize; }
            
            /* Hide specific columns if needed, or re-style */
            
            /* Print Header */
            .print-header { display: block !important; border-bottom: 4px double #000; padding-bottom: 15px; margin-bottom: 25px; text-align: center; }
            .print-header h3 { margin: 0; font-weight: 800; font-size: 18pt; text-transform: uppercase; letter-spacing: 1px; }
            .print-header h5 { margin: 5px 0; font-size: 14pt; font-weight: 600; }
            .print-header p { margin: 0; font-size: 10pt; }
            @page { size: A4 landscape; margin: 1.5cm; }
        }
    </style>
    @endpush

    <!-- Header Laporan (Hanya Muncul Saat Print) -->
    <div class="print-header d-none">
        <h3>SDN AENGBAJA KENEK II</h3>
        <h5>LAPORAN DATA PENGADUAN</h5>
        <p>Jalan Raya Aengbaja Kenek, Kecamatan Bluto, Kabupaten Sumenep, Jawa Timur</p>
    </div>

    <!-- Filter Section (Disembunyikan saat print) -->
    <div class="data-card mb-4 filter-card border-0 bg-white" style="box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <div class="bg-primary bg-opacity-10 text-primary rounded px-2 py-1 me-2">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                <h6 class="mb-0 fw-bold text-primary">Filter Laporan</h6>
            </div>
            
            <form action="{{ route('principal.reports.complaints') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Mulai Tanggal</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar text-muted"></i></span>
                            <input type="date" name="start_date" class="form-control border-start-0 ps-0" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check text-muted"></i></span>
                            <input type="date" name="end_date" class="form-control border-start-0 ps-0" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Status</label>
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
                        <label class="form-label small fw-semibold text-muted mb-1">Kategori</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-sm btn-primary-custom shadow-sm"><i class="bi bi-search me-1"></i>Terapkan</button>
                            <a href="{{ route('principal.reports.complaints') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="data-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-file-text me-2"></i>Data Laporan Pengaduan</h6>
            <button onclick="window.print()" class="btn btn-sm btn-primary-custom d-none d-md-inline-block btn-print">
                <i class="bi bi-printer me-1"></i> Cetak Laporan PDF
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
                                        <span
                                            class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
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
                                        <div class="small text-success fw-medium">Selesai:
                                            {{ $complaint->resolved_at->format('d/m/Y') }}</div>
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