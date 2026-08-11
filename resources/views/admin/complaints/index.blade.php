@extends('layouts.app')

@section('title', 'Manajemen Semua Pengaduan')
@section('page-title', 'Manajemen Semua Pengaduan')

@section('breadcrumb')
    <a href="#">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Manajemen Semua Pengaduan</span>
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
    <div class="data-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-inbox me-2"></i>Semua Pengaduan</h6>
        </div>
        <div class="card-body-custom p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>No Tiket</th>
                            <th>Pengirim</th>
                            <th>Kategori</th>
                            <th>Status / Prioritas</th>
                            <th>Tanggal</th>
                            <th>Nilai AHP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
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
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                            {{ $complaint->statusLabel }}
                                        </span>
                                        <span class="badge-status badge-priority-{{ $complaint->priority_level }}">
                                            {{ ucfirst($complaint->priority_level) }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($complaint->ahpResults->count() > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Sudah Dinilai</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ahpModal-{{ $complaint->id }}">
                                        <i class="bi bi-calculator"></i> Input AHP
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal Input AHP -->
                            <div class="modal fade" id="ahpModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.complaints.ahp.store', $complaint->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Input Nilai AHP Pengaduan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <p class="text-muted mb-4 small">
                                                    Tiket: <strong>{{ $complaint->tracking_code }}</strong><br>
                                                    {{ Str::limit($complaint->description, 100) }}
                                                </p>
                                                
                                                <div class="alert alert-info py-2 px-3 small border-0 bg-info bg-opacity-10">
                                                    Masukkan nilai intensitas (1 - 100) untuk setiap kriteria.
                                                </div>

                                                @foreach($criteria as $criterion)
                                                    @php
                                                        $existing = $complaint->ahpResults->where('criteria_id', $criterion->id)->first();
                                                        $val = $existing ? $existing->score : '';
                                                    @endphp
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">{{ $criterion->name }}</label>
                                                        <div class="input-group">
                                                            <input type="number" name="criteria[{{ $criterion->id }}]" class="form-control" value="{{ $val }}" min="1" max="100" required placeholder="Nilai (1-100)">
                                                        </div>
                                                        <div class="form-text mt-1" style="font-size: 0.70rem;">{{ $criterion->description }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary-custom px-4">Simpan Nilai</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted"> Belum ada data pengaduan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-top">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
@endsection
