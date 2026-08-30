@extends('layouts.app')

@section('title', 'Semua Pengaduan')
@section('page-title', 'Manajemen Semua Pengaduan')

@section('breadcrumb')
    <a href="#">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Semua Pengaduan</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Pengaduan</div>
    <a href="{{ route('admin.complaints.index') }}" class="nav-link active">
        <i class="bi bi-inbox"></i>
        <span>Semua Pengaduan</span>
    </a>

        <div class="nav-label">Pengaturan AHP</div>
    <a href="{{ route('admin.ahp.index') }}" class="nav-link {{ request()->routeIs('admin.ahp.index') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i>
        <span>Hasil AHP & Bobot</span>
    </a>
    <a href="{{ route('admin.ahp.comparison') }}" class="nav-link {{ request()->routeIs('admin.ahp.comparison') ? 'active' : '' }}">
        <i class="bi bi-table"></i>
        <span>Perbandingan Kriteria</span>
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
    <div class="data-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-inbox me-2"></i>Daftar Semua Pengaduan</h6>
        </div>
        <div class="card-body-custom p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger m-3">{{ session('error') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>No Tiket / Tanggal</th>
                            <th>Pengirim & Siswa</th>
                            <th>Kategori</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Prioritas</th>
                            <th class="text-center">Nilai AHP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: var(--primary)">{{ $complaint->tracking_code }}</span>
                                    <div class="small text-muted">{{ $complaint->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $complaint->parentUser->name ?? '-' }}</div>
                                    <div class="small text-muted">Siswa: {{ $complaint->student->name ?? '-' }}</div>
                                </td>
                                <td>{{ $complaint->category->name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge-status bg-{{ $complaint->statusBadge }} bg-opacity-10 text-{{ $complaint->statusBadge }}">
                                        {{ $complaint->statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(in_array($complaint->status, ['pending', 'rejected']))
                                        <span class="text-muted small">—</span>
                                    @else
                                        <span class="badge-status badge-priority-{{ $complaint->priority_level ?? 'low' }}">
                                            {{ strtoupper($complaint->priority_level ?? 'LOW') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(in_array($complaint->status, ['pending', 'rejected']))
                                        <span class="text-muted small">—</span>
                                    @else
                                        <span class="fw-bold">{{ $complaint->priority_score ? number_format((float)$complaint->priority_score, 4) : '—' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Tombol Detail --}}
                                    <button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $complaint->id }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($complaint->status === 'pending')
                                        {{-- Tombol Verifikasi: Terima / Tolak --}}
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $complaint->id }}" title="Terima Pengaduan">
                                            <i class="bi bi-check-lg"></i> Terima
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $complaint->id }}" title="Tolak Pengaduan">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    @elseif($complaint->status === 'approved')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $complaint->id }}" title="Ubah ke Diproses">
                                            <i class="bi bi-arrow-right-circle"></i> Proses
                                        </button>
                                    @elseif($complaint->status === 'on_progress')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $complaint->id }}" title="Tandai Selesai">
                                            <i class="bi bi-check2-all"></i> Selesai
                                        </button>
                                    @elseif($complaint->status === 'resolved')
                                        <span class="badge bg-success bg-opacity-10 text-success mb-1 d-inline-block">Selesai</span>
                                        <button type="button" class="btn btn-sm btn-success ms-1" data-bs-toggle="modal" data-bs-target="#waModal-{{ $complaint->id }}" title="Kirim Notifikasi WA">
                                            <i class="bi bi-whatsapp"></i> Notif WA
                                        </button>
                                    @elseif($complaint->status === 'rejected')
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Selesai / Ditolak</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal: Detail Pengaduan --}}
                            <div class="modal fade" id="detailModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-eye me-2"></i>Detail Pengaduan {{ $complaint->tracking_code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <div class="small fw-bold text-muted mb-1">Pengirim</div>
                                                    <div>{{ $complaint->parentUser->name ?? '-' }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="small fw-bold text-muted mb-1">Siswa</div>
                                                    <div>{{ $complaint->student->name ?? '-' }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="small fw-bold text-muted mb-1">Kategori</div>
                                                    <div>{{ $complaint->category->name ?? '-' }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="small fw-bold text-muted mb-1">Tanggal</div>
                                                    <div>{{ $complaint->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="small fw-bold text-muted mb-1">Deskripsi Pengaduan</div>
                                                <div class="bg-light p-3 rounded-3 text-dark" style="font-size: 0.85rem">{{ $complaint->description }}</div>
                                            </div>
                                            @if($complaint->evidence_path)
                                            <div class="mb-3">
                                                <div class="small fw-bold text-muted mb-1">Bukti Foto</div>
                                                <div class="text-center">
                                                    @php
                                                        $evidencePath = storage_path('app/public/' . $complaint->evidence_path);
                                                        $base64Image = null;
                                                        if (file_exists($evidencePath)) {
                                                            try {
                                                                $type = mime_content_type($evidencePath);
                                                                $data = file_get_contents($evidencePath);
                                                                if ($data) {
                                                                    $base64Image = 'data:' . $type . ';base64,' . base64_encode($data);
                                                                }
                                                            } catch (\Exception $e) {}
                                                        }
                                                    @endphp

                                                    @if($base64Image)
                                                        <img src="{{ $base64Image }}" alt="Bukti Foto" class="img-fluid rounded-3 border" style="max-height: 300px; object-fit: contain;">
                                                    @else
                                                        <span class="text-muted small"><i class="bi bi-image"></i> Gambar tidak tersedia/gagal dimuat.</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            @if($complaint->resolution_note)
                                            <div class="mb-3">
                                                <div class="small fw-bold text-muted mb-1">Catatan Penyelesaian</div>
                                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-dark border-start border-4 border-success" style="font-size: 0.85rem">{{ $complaint->resolution_note }}</div>
                                            </div>
                                            @endif
                                            @if($complaint->rejection_reason)
                                            <div class="mb-3">
                                                <div class="small fw-bold text-muted mb-1">Alasan Penolakan</div>
                                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-dark border-start border-4 border-danger" style="font-size: 0.85rem">{{ $complaint->rejection_reason }}</div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Modal: Kirim WA --}}
                            @if($complaint->status === 'resolved')
                            <div class="modal fade" id="waModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.complaints.resendWa', $complaint->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-success"><i class="bi bi-whatsapp me-2"></i>Kirim Notifikasi WhatsApp?</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <div class="mb-3">
                                                    <div class="small fw-bold text-muted mb-1">Penerima:</div>
                                                    <div>Bapak/Ibu {{ $complaint->parentUser->name ?? '-' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="small fw-bold text-muted mb-1">Nomor:</div>
                                                    <div class="fw-bold">{{ $complaint->parentUser->phone ?? '-' }}</div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="small fw-bold text-muted mb-1">Pesan:</div>
                                                    <div class="p-3 bg-light rounded text-dark" style="font-size: 0.85rem; white-space: pre-wrap;">Halo Bapak/Ibu,
Pengaduan Anda untuk siswa *{{ $complaint->student->name ?? '-' }}* dengan nomor tiket *{{ $complaint->tracking_code }}* telah *SELESAI* kami proses dan tindak lanjuti.

Catatan Penyelesaian:
{{ $complaint->resolution_note ?? '-' }}

Terima kasih atas laporan Anda. Silakan cek aplikasi untuk detail selengkapnya.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success px-4"><i class="bi bi-send-fill me-1"></i>Kirim WhatsApp</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            {{-- Modal: Terima --}}
                            @if($complaint->status === 'pending')
                            <div class="modal fade" id="approveModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.complaints.verify', $complaint->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle me-2"></i>Terima Pengaduan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <div class="alert alert-success bg-success bg-opacity-10 border-0 small">
                                                    Pengaduan <strong>{{ $complaint->tracking_code }}</strong> akan diverifikasi. 
                                                    Silakan beri nilai pada kondisi pengaduan berdasarkan kriteria AHP berikut untuk penentuan prioritas secara otomatis.
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <h6 class="fw-bold mb-3">Penilaian Kriteria AHP (Skala 1 - 5)</h6>
                                                    @foreach($criteria as $criterion)
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small mb-1">{{ $criterion->name }} ({{ $criterion->code }}) <span class="text-danger">*</span></label>
                                                            <div class="small text-muted mb-2" style="font-size: 0.7rem;">{{ $criterion->description }}</div>
                                                            <select name="criteria[{{ $criterion->id }}]" class="form-select form-select-sm" required>
                                                                <option value="">-- Pilih Nilai --</option>
                                                                <option value="1">1 - Sangat Rendah / Tidak Mendesak</option>
                                                                <option value="2">2 - Rendah / Kurang Mendesak</option>
                                                                <option value="3">3 - Sedang / Cukup Mendesak</option>
                                                                <option value="4">4 - Tinggi / Mendesak</option>
                                                                <option value="5">5 - Sangat Tinggi / Darurat</option>
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="text-muted small border-top pt-3">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Sistem akan mengalikan nilai yang Anda berikan dengan bobot kriteria AHP untuk menentukan prioritas akhir.
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg me-1"></i>Terima & Proses AHP</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal: Tolak --}}
                            <div class="modal fade" id="rejectModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.complaints.verify', $complaint->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Pengaduan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <div class="alert alert-warning bg-warning bg-opacity-10 border-0 small mb-3">
                                                    Anda akan menolak pengaduan <strong>{{ $complaint->tracking_code }}</strong>. Orang tua akan menerima notifikasi beserta alasan penolakan.
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                                                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Masukkan alasan penolakan yang jelas untuk orang tua..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger px-4"><i class="bi bi-x-lg me-1"></i>Tolak Pengaduan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            {{-- Modal: Update Status (on_progress / resolved) --}}
                            @if(in_array($complaint->status, ['approved', 'on_progress']))
                            <div class="modal fade" id="statusModal-{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.complaints.updateStatus', $complaint->id) }}" method="POST">
                                        @csrf
                                        @php
                                            $nextStatus = $complaint->status === 'approved' ? 'on_progress' : 'resolved';
                                            $nextLabel  = $nextStatus === 'on_progress' ? 'Sedang Ditindaklanjuti' : 'Selesai';
                                        @endphp
                                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-right-circle me-2"></i>Update Status Pengaduan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <p class="text-muted small">Ubah status <strong>{{ $complaint->tracking_code }}</strong> menjadi <strong class="text-primary">{{ $nextLabel }}</strong>.</p>
                                                @if($nextStatus === 'resolved')
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Catatan Penyelesaian (Opsional)</label>
                                                        <textarea name="resolution_note" class="form-control" rows="3" placeholder="Tuliskan catatan hasil penyelesaian pengaduan..."></textarea>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-check-lg me-1"></i>Konfirmasi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada data pengaduan.</td>
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
