@extends('layouts.app')

@section('title', 'Riwayat Pengaduan')
@section('page-title', 'Riwayat Pengaduan')

@section('breadcrumb')
    <a href="#">Wali Murid</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Riwayat Pengaduan</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('parent.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Pengaduan</div>
    <a href="{{ route('parent.complaints.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Tiket Baru</span>
    </a>
    <a href="{{ route('parent.complaints.index') }}" class="nav-link active">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat Pengaduan</span>
    </a>
@endsection

@section('content')
    <div class="data-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-inbox me-2"></i>Daftar Riwayat Pengaduan Saya</h6>
            <a href="{{ route('parent.complaints.create') }}" class="btn btn-sm btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i> Tiket Baru
            </a>
        </div>
        <div class="card-body-custom p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No Tiket</th>
                            <th>Siswa / Kategori</th>
                            <th>Tanggal Laporan</th>
                            <th>Status & Progress</th>
                            <th class="text-center">Aksi / Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>
                                    <span class="fw-bold fs-6" style="color: var(--primary)">{{ $complaint->tracking_code }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $complaint->student->name ?? '-' }}</div>
                                    <div class="small text-muted">{{ $complaint->category->name ?? '-' }}</div>
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                            {{ $complaint->statusLabel }}
                                        </span>
                                        @if($complaint->status == 'resolved' && $complaint->resolved_at)
                                            <span class="small text-success"><i class="bi bi-check-all"></i> {{ $complaint->resolved_at->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $complaint->id }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                    
                                    @if($complaint->status == 'resolved')
                                        @if($complaint->rating)
                                            <button type="button" class="btn btn-sm btn-outline-warning disabled ms-1" title="Anda sudah memberikan evaluasi">
                                                <i class="bi bi-star-fill"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-warning ms-1" data-bs-toggle="modal" data-bs-target="#rateModal-{{ $complaint->id }}" title="Beri Penilaian">
                                                <i class="bi bi-star"></i> Nilai
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Detail Tiket {{ $complaint->tracking_code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-4 text-start">
                                            <div class="alert bg-{{ $complaint->statusBadge }} bg-opacity-10 border-0 mb-4 py-2 d-flex align-items-center">
                                                <div class="fw-bold text-{{ $complaint->statusBadge }} me-2">Status:</div>
                                                <span class="text-{{ $complaint->statusBadge }}">{{ $complaint->statusLabel }}</span>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted mb-1">Deskripsi Pengaduan:</label>
                                                <div class="bg-light p-3 rounded-3 text-dark" style="font-size: 0.85rem">
                                                    {{ $complaint->description }}
                                                </div>
                                            </div>

                                            @if($complaint->evidence_path)
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted mb-1">Bukti Foto:</label>
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/' . $complaint->evidence_path) }}" alt="Bukti Foto" class="img-fluid rounded-3 border" style="max-height: 300px; object-fit: contain;">
                                                </div>
                                            </div>
                                            @endif

                                            @if($complaint->responses)
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted mb-1">Tanggapan/Catatan Sekolah:</label>
                                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-dark border-start border-4 border-primary" style="font-size: 0.85rem">
                                                    {{ $complaint->responses }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($complaint->status == 'resolved' && !$complaint->rating)
                            <!-- Modal Rate -->
                            <div class="modal fade" id="rateModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <form action="{{ route('parent.complaints.rate', $complaint->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold text-warning"><i class="bi bi-star-fill me-1"></i> Penilaian Layanan</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start py-4">
                                                <div class="text-center mb-3">
                                                    <p class="small text-muted">Berapa bintang kepuasan Anda terhadap penyelesaian pengaduan tiket ini?</p>
                                                    <div class="rating-input d-flex flex-row-reverse justify-content-center gap-2">
                                                        <input type="radio" id="star5-{{ $complaint->id }}" name="rating" value="5" class="d-none"><label for="star5-{{ $complaint->id }}" class="fs-2 text-muted" style="cursor: pointer"><i class="bi bi-star-fill"></i></label>
                                                        <input type="radio" id="star4-{{ $complaint->id }}" name="rating" value="4" class="d-none"><label for="star4-{{ $complaint->id }}" class="fs-2 text-muted" style="cursor: pointer"><i class="bi bi-star-fill"></i></label>
                                                        <input type="radio" id="star3-{{ $complaint->id }}" name="rating" value="3" class="d-none"><label for="star3-{{ $complaint->id }}" class="fs-2 text-muted" style="cursor: pointer"><i class="bi bi-star-fill"></i></label>
                                                        <input type="radio" id="star2-{{ $complaint->id }}" name="rating" value="2" class="d-none"><label for="star2-{{ $complaint->id }}" class="fs-2 text-muted" style="cursor: pointer"><i class="bi bi-star-fill"></i></label>
                                                        <input type="radio" id="star1-{{ $complaint->id }}" name="rating" value="1" class="d-none"><label for="star1-{{ $complaint->id }}" class="fs-2 text-muted" style="cursor: pointer"><i class="bi bi-star-fill"></i></label>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold">Ulasan (Opsional)</label>
                                                    <textarea name="comment" class="form-control form-control-sm" rows="3" placeholder="Tuliskan umpan balik Anda di sini..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Nanti</button>
                                                <button type="submit" class="btn btn-sm btn-primary-custom px-3">Kirim Penilaian</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Riwayat pengaduan Anda masih kosong.</td>
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

@push('styles')
<style>
    .rating-input > input:checked ~ label {
        color: #f59e0b !important;
    }
    .rating-input > label:hover,
    .rating-input > label:hover ~ label {
        color: #fbbf24 !important;
    }
</style>
@endpush
