@extends('layouts.app')

@section('title', 'Ranking Prioritas AHP')
@section('page-title', 'Pemantauan Hasil Pengaduan')

@section('breadcrumb')
    <a href="#">Kepala Sekolah</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Ranking AHP</span>
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
    <a href="{{ route('principal.reports.evaluation') }}" class="nav-link">
        <i class="bi bi-star"></i>
        <span>Evaluasi Kepuasan</span>
    </a>
@endsection

@section('content')
    <div class="data-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-sort-numeric-down me-2"></i>Hasil Pengaduan (Berdasarkan Sistem AHP)</h6>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">Rank</th>
                            <th>No Tiket</th>
                            <th>Judul Pengaduan</th>
                            <th>Kategori</th>
                            <th class="text-center">Nilai AHP</th>
                            <th class="text-center">Prioritas</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $index => $complaint)
                            <tr>
                                <td class="text-center">
                                    @php
                                        // Rank logic accounting for pagination
                                        $rank = ($complaints->currentPage() - 1) * $complaints->perPage() + $index + 1;
                                    @endphp
                                    <span
                                        class="badge rounded-circle {{ $rank <= 3 ? 'bg-danger text-white' : 'bg-light text-dark border' }}"
                                        style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-weight: 600;">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold" style="color: var(--primary)">{{ $complaint->tracking_code }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium text-truncate" style="max-width: 250px;"
                                        title="{{ $complaint->title }}">
                                        {{ $complaint->title }}
                                    </div>
                                    <div class="small text-muted">{{ $complaint->parentUser->name ?? '-' }}</div>
                                </td>
                                <td>{{ $complaint->category->name ?? '-' }}</td>
                                <td class="text-center fw-bold fs-6">
                                    {{ number_format((float) $complaint->priority_score, 4) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge-status badge-priority-{{ $complaint->priority_level ?? 'low' }}">
                                        {{ strtoupper($complaint->priority_level ?? 'LOW') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                        {{ $complaint->statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bi bi-inbox fs-2"></i></div>
                                    <p class="mb-0">Belum ada pengaduan yang telah diproses oleh AHP.</p>
                                </td>
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