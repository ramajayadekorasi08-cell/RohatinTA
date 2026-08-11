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
    <a href="{{ route('admin.ahp.index') }}" class="nav-link">
        <i class="bi bi-diagram-3"></i>
        <span>Hitung AHP</span>
    </a>
    
    <div class="nav-label">Master Data</div>
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
    <div class="row g-3 mb-4">
        <!-- Total -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-hexagon-fill"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Pengaduan</div>
            </div>
        </div>
        
        <!-- Pending -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">Diterima</div>
            </div>
        </div>

        <!-- On Progress -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['on_progress'] }}</div>
                <div class="stat-label">Diproses</div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-all"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['resolved'] }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Ditolak</div>
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
