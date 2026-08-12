@extends('layouts.app')

@section('title', 'Buat Tiket Baru')
@section('page-title', 'Buat Tiket Pengaduan Baru')

@section('breadcrumb')
    <a href="#">Wali Murid</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Buat Tiket Baru</span>
@endsection

@section('sidebar')
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('parent.dashboard') }}" class="nav-link">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
    </a>
    
    <div class="nav-label">Pengaduan</div>
    <a href="{{ route('parent.complaints.create') }}" class="nav-link active">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Tiket Baru</span>
    </a>
    <a href="{{ route('parent.complaints.index') }}" class="nav-link">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat Pengaduan</span>
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="data-card mb-4">
                <div class="card-header-custom bg-white">
                    <h6><i class="bi bi-pencil-square me-2"></i>Form Buat Tiket Pengaduan</h6>
                </div>
                <div class="card-body-custom pt-4">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('parent.complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Data Siswa</label>
                            <div class="card border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                        <i class="bi bi-person-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $activeStudent->name }}</div>
                                        <div class="small text-muted">NISN: {{ $activeStudent->nis }} — Kelas {{ $activeStudent->class }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text small">Pengaduan ini akan ditujukan untuk siswa di atas. Untuk mengganti, ubah pilihan anak di <a href="{{ route('parent.dashboard') }}">Dashboard</a>.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Kategori Pengaduan</label>
                            <div class="row g-3">
                                @foreach($categories as $category)
                                    <div class="col-md-6">
                                        <label class="position-relative w-100 h-100" style="cursor: pointer;">
                                            <input type="radio" name="category_id" value="{{ $category->id }}" class="btn-check" required>
                                            <div class="card h-100 border p-3 rounded-3 category-card transition" style="transition: all 0.2s">
                                                <div class="fw-bold mb-1">{{ $category->name }}</div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">{{ Str::limit($category->description, 60) }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Deskripsi Laporan / Pengaduan</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Jelaskan detail pengaduan Anda di sini secara lengkap (min. 10 karakter)..." required minlength="10"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Foto Bukti (Opsional)</label>
                            <input type="file" name="evidence" class="form-control" accept="image/jpeg,image/png,image/gif">
                            <div class="form-text small">Unggah foto bukti jika ada (Maksimal 2MB, format jpg/jpeg/png/gif).</div>
                        </div>

                        <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex gap-3 align-items-center mb-4">
                            <i class="bi bi-info-circle-fill text-info fs-4"></i>
                            <div class="small text-dark">
                                Setelah dikirim, laporan akan diverifikasi oleh pihak sekolah. Notifikasi tanggapan akan dikirimkan otomatis melalui sistem atau kontak WhatsApp yang terdaftar.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 p-3 bg-light rounded-3 border">
                            <button type="reset" class="btn btn-light"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
                            <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-send me-1"></i>Kirim Pengaduan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .btn-check:checked + .category-card {
        border-color: var(--primary) !important;
        background-color: rgba(30,58,95,0.05);
        box-shadow: 0 0 0 1px var(--primary);
    }
    .category-card:hover { border-color: #cbd5e1; background: #f8fafc; }
</style>
@endpush
