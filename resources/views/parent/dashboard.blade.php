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
        <div class="col-md-7">
            <h5 class="fw-bold text-dark mb-1">Selamat Datang, {{ auth()->user()->name }}</h5>
            <p class="text-muted mb-0">
                @if(isset($activeStudent) && $activeStudent)
                    Monitoring laporan untuk: <strong class="text-primary">{{ $activeStudent->name }}</strong>
                @else
                    Pantau status laporan dan riwayat pengaduan Anda di sini.
                @endif
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end flex-wrap">
            @if(isset($students) && $students->count() > 1)
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-badge"></i> {{ isset($activeStudent) && $activeStudent ? $activeStudent->name : 'Pilih Anak' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:250px;">
                        <li class="px-3 py-2 text-muted small bg-light border-bottom">Pilih data siswa:</li>
                        @foreach($students as $student)
                            <li>
                                <form action="{{ route('parent.switch_student', $student->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 {{ (isset($activeStudentId) && $activeStudentId == $student->id) ? 'active fw-bold' : '' }}">
                                        {{ $student->name }} <small class="d-block {{ (isset($activeStudentId) && $activeStudentId == $student->id) ? 'text-white-50' : 'text-muted' }}">Kelas: {{ $student->class }}</small>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <a href="{{ route('parent.complaints.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i> Buat Tiket Pengaduan
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <!-- Single Panel Statistik Pengaduan Parent -->
    <div class="data-card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header-custom bg-white border-bottom py-3 px-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                <i class="bi bi-person-lines-fill fs-5"></i>
            </div>
            <h6 class="mb-0 fw-bold class-title">STATUS PENGADUAN {{ isset($activeStudent) && $activeStudent ? strtoupper($activeStudent->name) : 'SAYA' }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 text-center">
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-muted small fw-bold mb-1 text-uppercase">Total</p>
                    <h3 class="fw-black text-dark mb-0">{{ $stats['total'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-warning small fw-bold mb-1 text-uppercase">Pending</p>
                    <h3 class="fw-black text-warning mb-0">{{ $stats['pending'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-end border-bottom p-4">
                    <p class="text-info small fw-bold mb-1 text-uppercase">Diproses</p>
                    <h3 class="fw-black text-info mb-0">{{ $stats['on_progress'] }}</h3>
                </div>
                <div class="col-6 col-md-3 border-bottom p-4">
                    <p class="text-success small fw-bold mb-1 text-uppercase">Selesai</p>
                    <h3 class="fw-black text-success mb-0">{{ $stats['resolved'] }}</h3>
                </div>
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
