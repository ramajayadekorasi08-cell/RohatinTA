@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard Kepsek')

@section('breadcrumb')
    <a href="#">Kepala Sekolah</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Dashboard</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('principal.dashboard') }}" class="nav-link active">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Monitoring</div>
    <a href="{{ route('principal.ahp.index') }}" class="nav-link">
        <i class="bi bi-bar-chart-line"></i>
        <span>Prioritas Pengaduan</span>
    </a>

    <div class="nav-label">Laporan</div>
    <a href="{{ route('principal.reports.complaints') }}" class="nav-link">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan Pengaduan</span>
    </a>
    <a href="{{ route('principal.reports.evaluation') }}" class="nav-link">
        <i class="bi bi-star"></i>
        <span>Laporan Evaluasi</span>
    </a>
@endsection

@section('content')
    <!-- Dashboard Stats -->
    <!-- Single Panel Statistik Pengaduan Kepsek -->
    <div class="data-card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header-custom bg-white border-bottom py-3 px-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                <i class="bi bi-display fs-5"></i>
            </div>
            <h6 class="mb-0 fw-bold class-title">MONITORING PENGADUAN</h6>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 text-center">
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-muted small fw-bold mb-1 text-uppercase">Total Pengaduan</p>
                    <h3 class="fw-black text-dark mb-0">{{ $stats['total'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-warning small fw-bold mb-1 text-uppercase">Pengaduan Aktif</p>
                    <h3 class="fw-black text-warning mb-0">{{ $stats['active'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-success small fw-bold mb-1 text-uppercase">Pengaduan Selesai</p>
                    <h3 class="fw-black text-success mb-0">{{ $stats['resolved'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-bottom p-4">
                    <p class="text-info small fw-bold mb-1 text-uppercase">Avg. Evaluasi</p>
                    <h3 class="fw-black text-info mb-0">{{ number_format($stats['avg_rating'], 1) }} <small class="fs-6 text-muted">/ 5</small></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Status Chart -->
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-pie-chart me-2"></i>Status Pengaduan</h6>
                </div>
                <div class="card-body-custom d-flex justify-content-center align-items-center" style="min-height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Trend Chart -->
        <div class="col-lg-8">
            <div class="data-card h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-graph-up me-2"></i>Tren Pengaduan (6 Bulan)</h6>
                </div>
                <div class="card-body-custom" style="position: relative; height:250px; width:100%">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- High Priority Complaints Table -->
        <div class="col-lg-12">
            <div class="data-card">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Pengaduan Prioritas Tinggi (Belum Selesai)</h6>
                    <a href="{{ route('principal.reports.complaints') }}" class="btn btn-sm btn-outline-primary">Lihat Laporan Lengkap <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No Tiket</th>
                                    <th>Pengirim</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($highPriorityComplaints as $complaint)
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
                                            <span class="badge bg-danger">HIGH ({{ number_format($complaint->priority_score * 100, 1) }})</span>
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
                                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada pengaduan prioritas tinggi aktif.</td>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const trendCtx = document.getElementById('trendChart').getContext('2d');

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusChart['labels']) !!},
                datasets: [{
                    data: {!! json_encode($statusChart['data']) !!},
                    backgroundColor: [
                        '#f59e0b', // pending
                        '#3b82f6', // approved
                        '#0ea5e9', // on progress
                        '#10b981', // resolved
                        '#ef4444'  // rejected
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } }
                },
                cutout: '70%'
            }
        });

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendChart['labels']) !!},
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: {!! json_encode($trendChart['data']) !!},
                    borderColor: '#2c5282',
                    backgroundColor: 'rgba(44, 82, 130, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#2c5282'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
