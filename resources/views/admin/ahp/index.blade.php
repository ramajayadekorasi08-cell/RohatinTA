@extends('layouts.app')

@section('title', 'Hitung AHP')
@section('page-title', 'Hitung AHP')

@section('breadcrumb')
    <a href="#">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Hitung AHP</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Manajemen</div>
    <a href="{{ route('admin.complaints.index') }}" class="nav-link">
        <i class="bi bi-inbox"></i>
        <span>Semua Pengaduan</span>
    </a>
    <a href="{{ route('admin.ahp.index') }}" class="nav-link active">
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
    <div class="row g-4 mb-4">
        <!-- Criteria Weights Card -->
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-bar-chart-steps me-2"></i>Bobot Kriteria AHP</h6>
                </div>
                <div class="card-body-custom">
                    <ul class="list-group list-group-flush">
                        @foreach($criteria as $criterion)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                <div>
                                    <div class="fw-bold text-dark">{{ $criterion->name }} ({{ $criterion->code }})</div>
                                    <div class="small text-muted">{{ $criterion->description }}</div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">{{ round($criterion->weight * 100, 2) }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Ranking and Calculation Card -->
        <div class="col-lg-8">
            <div class="data-card h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6><i class="bi bi-sort-numeric-down me-2"></i>Hasil Ranking Pengaduan</h6>
                    <form action="{{ route('admin.ahp.calculate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary-custom">
                            <i class="bi bi-arrow-clockwise me-1"></i> Proses Hitung AHP
                        </button>
                    </form>
                </div>
                <div class="card-body-custom p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">{{ session('success') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Rank</th>
                                    <th>No Tiket</th>
                                    <th>Nilai Total AHP</th>
                                    <th>Status Prioritas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complaints as $index => $complaint)
                                    <tr>
                                        <td class="ps-4">
                                            @if($index == 0)
                                                <span class="badge bg-warning text-dark fs-6 rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold;">1</span>
                                            @elseif($index == 1)
                                                <span class="badge bg-secondary fs-6 rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold;">2</span>
                                            @elseif($index == 2)
                                                <span class="badge" style="background-color: #cd7f32; font-size: 1rem; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold;">3</span>
                                            @else
                                                <span class="fw-bold text-muted ps-2">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-weight: 600; color: var(--primary)">{{ $complaint->tracking_code }}</span>
                                            <div class="small text-muted">{{ $complaint->category->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark fs-6">{{ number_format($complaint->priority_score * 100, 2) }}</div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-priority-{{ $complaint->priority_level }}">
                                                {{ ucfirst($complaint->priority_level) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada pengaduan yang dinilai AHP</td>
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
