@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <a href="#">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Dashboard</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Manajemen</div>
    <a href="{{ route('admin.complaints.index') }}" class="nav-link">
        <i class="bi bi-inbox"></i>
        <span>Semua Pengaduan</span>
        @if($stats['pending'] > 0)
            <span class="nav-badge">{{ $stats['pending'] }}</span>
        @endif
    </a>

    
    <div class="nav-label">Master Data</div>
    <a href="{{ route('admin.students.index') }}" class="nav-link">
        <i class="bi bi-mortarboard"></i>
        <span>Data Siswa</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="nav-link">
        <i class="bi bi-folder2"></i>
        <span>Kategori Pengaduan</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="bi bi-people"></i>
        <span>Data Pengguna</span>
    </a>
@endsection

@section('content')
    <!-- Single Panel Statistik Pengaduan -->
    <div class="data-card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header-custom bg-white border-bottom py-3 px-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                <i class="bi bi-bar-chart-fill fs-5"></i>
            </div>
            <h6 class="mb-0 fw-bold class-title">STATISTIK PENGADUAN KESELURUHAN</h6>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 text-center">
                <div class="col-6 col-md-4 col-xl-2 border-end border-bottom p-4">
                    <p class="text-muted small fw-bold mb-1 text-uppercase">Total</p>
                    <h3 class="fw-black text-dark mb-0">{{ $stats['total'] }}</h3>
                </div>
                <div class="col-6 col-md-4 col-xl-2 border-end border-bottom p-4">
                    <p class="text-warning small fw-bold mb-1 text-uppercase">Pending</p>
                    <h3 class="fw-black text-warning mb-0">{{ $stats['pending'] }}</h3>
                </div>
                <div class="col-6 col-md-4 col-xl-2 border-end border-bottom p-4">
                    <p class="text-info small fw-bold mb-1 text-uppercase">Diterima</p>
                    <h3 class="fw-black text-info mb-0">{{ $stats['approved'] }}</h3>
                </div>
                <div class="col-6 col-md-4 col-xl-2 border-end border-bottom p-4">
                    <p class="text-primary small fw-bold mb-1 text-uppercase">Diproses</p>
                    <h3 class="fw-black text-primary mb-0">{{ $stats['on_progress'] }}</h3>
                </div>
                <div class="col-6 col-md-4 col-xl-2 border-end border-bottom p-4">
                    <p class="text-success small fw-bold mb-1 text-uppercase">Selesai</p>
                    <h3 class="fw-black text-success mb-0">{{ $stats['resolved'] }}</h3>
                </div>
                <div class="col-6 col-md-4 col-xl-2 border-bottom p-4">
                    <p class="text-danger small fw-bold mb-1 text-uppercase">Ditolak</p>
                    <h3 class="fw-black text-danger mb-0">{{ $stats['rejected'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="data-card h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-graph-up me-2"></i>Tren Pengaduan (6 Bulan)</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="trendChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-pie-chart me-2"></i>Status Pengaduan</h6>
                </div>
                <div class="card-body-custom d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card mb-4">
        <div class="card-header-custom">
            <h6><i class="bi bi-clock-history me-2"></i>Pengaduan Terbaru</h6>
            <button class="btn btn-sm btn-primary-custom">Lihat Semua</button>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>No Tiket</th>
                            <th>Pengirim</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentComplaints as $complaint)
                            <tr>
                                <td><span style="font-weight: 600; color: var(--primary)">{{ $complaint->tracking_code }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span style="font-weight: 500">{{ $complaint->parentUser->name ?? '-' }}</span>
                                        <span style="color: #64748b; font-size: 0.75rem">Siswa: {{ $complaint->student->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $complaint->category->name ?? '-' }}</td>
                                <td>
                                    <span style="font-weight: 500" class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $complaint->title }}">
                                        {{ $complaint->title }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status badge-priority-{{ $complaint->priority_level }}">
                                        {{ ucfirst($complaint->priority_level) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                        {{ $complaint->statusLabel }}
                                    </span>
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada pengaduan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusChart['labels']) !!},
                datasets: [{
                    data: {!! json_encode($statusChart['data']) !!},
                    backgroundColor: [
                        '#f59e0b', // Pending
                        '#0ea5e9', // Approved
                        '#3b82f6', // On Progress
                        '#10b981', // Resolved
                        '#ef4444'  // Rejected
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 15, font: { family: 'Inter', size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendChart['labels']) !!},
                datasets: [{
                    label: 'Pengaduan Masuk',
                    data: {!! json_encode($trendChart['data']) !!},
                    borderColor: '#1e3a5f',
                    backgroundColor: 'rgba(30,58,95,0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1e3a5f',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: 'rgba(226,232,240,0.5)', drawBorder: false }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });
    });
</script>
@endpush
