@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa & Wali Murid')

@section('breadcrumb')
    <a href="#">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Data Siswa</span>
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

    <div class="nav-label">Master Data</div>
    <a href="{{ route('admin.students.index') }}" class="nav-link active">
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
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h6><i class="bi bi-mortarboard me-2"></i>Daftar Siswa</h6>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Search & Filters -->
                <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex gap-2">
                    <select name="class_filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('class_filter') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIS/Siswa..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>
                    @if(request()->has('search') || request()->has('class_filter'))
                        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-light" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
                <button class="btn btn-primary-custom btn-sm ms-md-2" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg"></i> Tambah Siswa
                </button>
            </div>
        </div>
        <div class="card-body-custom p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger m-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS/NISN</th>
                            <th>Nama Siswa</th>
                            <th>L/P</th>
                            <th>Kelas</th>
                            <th>Nama Orang Tua</th>
                            <th>No WhatsApp</th>
                            <th>Status Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr>
                                <td>{{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</td>
                                <td><span style="font-weight: 600; color: var(--primary);">{{ $student->nis }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->gender ?? '-' }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $student->class }}</span></td>
                                <td>{{ $student->parent->name ?? '-' }}</td>
                                <td>{{ $student->parent->phone ?? '-' }}</td>
                                <td>
                                    @if($student->parent)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2"><i class="bi bi-check-circle me-1"></i>Tersedia</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $student->id }}" title="Detail Lengkap">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal-{{ $student->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $student->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Detail Modal -->
                            <div class="modal fade" id="detailModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Detail Siswa & Orang Tua</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-4">
                                            <div class="row g-3">
                                                <div class="col-12"><h6 class="border-bottom pb-2 text-primary">Data Siswa</h6></div>
                                                <div class="col-md-4 text-muted small fw-semibold">NIS/NISN</div>
                                                <div class="col-md-8">{{ $student->nis }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Nama Siswa</div>
                                                <div class="col-md-8">{{ $student->name }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Jenis Kelamin</div>
                                                <div class="col-md-8">{{ $student->gender == 'L' ? 'Laki-Laki' : ($student->gender == 'P' ? 'Perempuan' : '-') }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Tempat/Tgl Lahir</div>
                                                <div class="col-md-8">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d-m-Y') : '-' }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Kelas</div>
                                                <div class="col-md-8"><span class="badge bg-secondary">{{ $student->class }}</span></div>
                                                <div class="col-md-4 text-muted small fw-semibold">Alamat</div>
                                                <div class="col-md-8">{{ $student->address ?? '-' }}</div>

                                                <div class="col-12 mt-4"><h6 class="border-bottom pb-2 text-primary">Data Orang Tua / Akun</h6></div>
                                                <div class="col-md-4 text-muted small fw-semibold">Nama Orang Tua</div>
                                                <div class="col-md-8">{{ $student->parent->name ?? '-' }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">No. WhatsApp</div>
                                                <div class="col-md-8">{{ $student->parent->phone ?? '-' }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Email</div>
                                                <div class="col-md-8">{{ $student->parent->email ?? '-' }}</div>
                                                <div class="col-md-4 text-muted small fw-semibold">Username Login</div>
                                                <div class="col-md-8 fw-bold text-success">{{ $student->parent->username ?? '-' }}</div>
                                                
                                                @if($student->parent && $student->parent->students->count() > 1)
                                                <div class="col-12 mt-4"><h6 class="border-bottom pb-2 text-primary">Daftar Anak Wali Murid Ini</h6></div>
                                                <div class="col-12">
                                                    <ul class="list-group">
                                                        @foreach($student->parent->students as $sibling)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $sibling->name }}</strong> - Kelas {{ $sibling->class }}
                                                            </div>
                                                            <span class="badge bg-primary rounded-pill">NISN: {{ $sibling->nis }}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Siswa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-4 text-start">
                                                
                                                <h6 class="mb-3 text-primary border-bottom pb-2"><i class="bi bi-person me-2"></i>Identitas Siswa</h6>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">NIS/NISN <span class="text-danger">*</span></label>
                                                        <input type="text" name="nis" class="form-control" value="{{ $student->nis }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                                                        <select name="gender" class="form-select" required>
                                                            <option value="" disabled>Pilih...</option>
                                                            <option value="L" {{ $student->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                            <option value="P" {{ $student->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
                                                        <input type="text" name="class" class="form-control" value="{{ $student->class }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold small">Tahun Masuk <span class="text-danger">*</span></label>
                                                        <input type="number" name="tahun_masuk" class="form-control" value="{{ $student->tahun_masuk }}" required min="2000">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold small">Tempat Lahir</label>
                                                        <input type="text" name="birth_place" class="form-control" value="{{ $student->birth_place }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                                        <input type="date" name="birth_date" class="form-control" value="{{ $student->birth_date }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Alamat</label>
                                                        <input type="text" name="address" class="form-control" value="{{ $student->address }}">
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 text-primary border-bottom pb-2"><i class="bi bi-people me-2"></i>Data Orang Tua (Akun)</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Nama Orang Tua/Wali <span class="text-danger">*</span></label>
                                                        <input type="text" name="parent_name" class="form-control" value="{{ $student->parent->name ?? '' }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                                                        <input type="text" name="parent_phone" class="form-control" placeholder="Awalan 62 atau 08..." value="{{ $student->parent->phone ?? '' }}" required>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold small">Email (Opsional)</label>
                                                        <input type="email" name="parent_email" class="form-control" value="{{ $student->parent->email ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary-custom px-4">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Siswa?</h6>
                                            </div>
                                            <div class="modal-body text-start">
                                                Apakah Anda yakin ingin menghapus siswa <strong>{{ $student->name }}</strong>?<br><br>
                                                <span class="text-danger small fw-bold">Perhatian: Akun Orang Tua hanya akan terhapus jika tidak memiliki anak lain lagi.</span>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger px-3">Hapus Selamanya</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-mortarboard fs-2 d-block mb-3"></i>
                                    Belum ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($students->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.students.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Tambah Data Siswa Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4 text-start">
                        
                        <div class="alert alert-info bg-info bg-opacity-10 border-0 small mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Pembuatan Akun Otomatis:</strong> Ketika data ini disimpan, sistem akan mencari akun Orang Tua berdasarkan <strong>Nomor WhatsApp</strong>.
                            Jika belum terdaftar, akun baru akan dibuat otomatis (Username: <strong>nama ortu tanpa spasi</strong>, Password: <strong>sipadu + tahun masuk</strong>).
                            Jika sudah terdaftar, siswa akan langsung dihubungkan ke akun yang ada.
                        </div>

                        <h6 class="mb-3 text-primary border-bottom pb-2"><i class="bi bi-person me-2"></i>Identitas Siswa</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">NIS/NISN <span class="text-danger">*</span></label>
                                <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih...</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="class" class="form-control" placeholder="Contoh: VII A" value="{{ old('class') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tahun Masuk <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_masuk" class="form-control" placeholder="Contoh: 2026" value="{{ old('tahun_masuk') ?? date('Y') }}" required min="2000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tempat Lahir</label>
                                <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Alamat Lengkap</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                            </div>
                        </div>

                        <h6 class="mb-3 text-primary border-bottom pb-2"><i class="bi bi-people me-2"></i>Data Orang Tua (Akun Wali)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Orang Tua/Wali <span class="text-danger">*</span></label>
                                <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="parent_phone" class="form-control" placeholder="Awalan 62xxx atau 08xxx" value="{{ old('parent_phone') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Email Utama (Opsional)</label>
                                <input type="email" name="parent_email" class="form-control" placeholder="Akan digunakan jika ada reset password" value="{{ old('parent_email') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-save me-2"></i>Simpan Siswa & Buat Akun</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
