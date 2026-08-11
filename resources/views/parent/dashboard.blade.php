@extends('layouts.app')

@section('title', 'Dashboard Wali Murid')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <a href="#">Wali Murid</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Dashboard</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('parent.dashboard') }}" class="nav-link active">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Pengaduan</div>
    <a href="{{ route('parent.complaints.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Tiket Baru</span>
    </a>
    <a href="{{ route('parent.complaints.index') }}" class="nav-link">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat Pengaduan</span>
    </a>
@endsection

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h5 class="fw-bold text-dark mb-1">Selamat Datang, {{ auth()->user()->name }}</h5>
            <p class="text-muted mb-0">Pantau status laporan dan riwayat pengaduan Anda di sini.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('parent.complaints.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i> Buat Tiket Pengaduan Baru
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-label">Total Pengaduan</div>
                    <div class="stat-icon text-primary bg-primary bg-opacity-10">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-label">Menunggu Respon (Pending)</div>
                    <div class="stat-icon text-warning bg-warning bg-opacity-10">
                        <i class="bi bi-hourglass-top"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-label">Sedang Diproses</div>
                    <div class="stat-icon text-info bg-info bg-opacity-10">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['on_progress'] }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-label">Telah Selesai</div>
                    <div class="stat-icon text-success bg-success bg-opacity-10">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['resolved'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="data-card">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6><i class="bi bi-clock-history me-2"></i>Pengaduan Terbaru Anda</h6>
                    <a href="{{ route('parent.complaints.index') }}" class="btn btn-sm btn-outline-secondary text-decoration-none">Lihat Semua Riwayat</a>
                </div>
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No Tiket</th>
                                    <th>Siswa</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComplaints as $complaint)
                                    <tr>
                                        <td><span class="fw-bold" style="color: var(--primary)">{{ $complaint->tracking_code }}</span></td>
                                        <td>{{ $complaint->student->name ?? '-' }}</td>
                                        <td>{{ $complaint->category->name ?? '-' }}</td>
                                        <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                                {{ $complaint->statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pengaduan sama sekali.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
