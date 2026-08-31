@extends('layouts.app')

@section('title', 'Perbandingan AHP')
@section('page-title', 'Perbandingan Kriteria AHP')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <a href="{{ route('admin.ahp.index') }}">AHP</a>
    <i class="bi bi-chevron-right" style="font-size: 0.6rem"></i>
    <span>Perbandingan Kriteria</span>
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
    
    <div class="nav-label">Pengaturan AHP</div>
    <a href="{{ route('admin.ahp.index') }}" class="nav-link {{ request()->routeIs('admin.ahp.index') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i>
        <span>Hasil Pengaduan & Bobot</span>
    </a>
    <a href="{{ route('admin.ahp.comparison') }}" class="nav-link active">
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
    <div class="data-card border-0 shadow-sm rounded-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-table me-2"></i>Matriks Perbandingan Berpasangan</h6>
            <a href="{{ route('admin.ahp.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="card-body p-4">
            <div class="alert alert-info">
                <strong>Panduan Skala Saaty:</strong><br/>
                1 = Sama penting <br/>
                3 = Sedikit lebih penting <br/>
                5 = Lebih penting <br/>
                7 = Sangat lebih penting <br/>
                9 = Mutlak lebih penting <br/>
                (Gunakan nilai antara 2,4,6,8 jika ragu). Nilai otomatis akan menyesuaikan untuk sel diagonal kebalikannya.
            </div>

            <form action="{{ route('admin.ahp.storeComparison') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Kriteria</th>
                                @foreach($criteria as $col)
                                    <th>{{ $col->code }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criteria as $row)
                                <tr>
                                    <th class="bg-light text-start">{{ $row->code }} - {{ $row->name }}</th>
                                    @foreach($criteria as $col)
                                        @php
                                            $val = 1;
                                            if ($row->id != $col->id) {
                                                $comp = $comparisons->where('criteria_row_id', $row->id)
                                                                    ->where('criteria_col_id', $col->id)->first();
                                                if ($comp) $val = $comp->value;
                                            }
                                        @endphp
                                        
                                        @if($row->id == $col->id)
                                            <td class="bg-secondary text-white fw-bold">1</td>
                                            <input type="hidden" name="comparisons[{{ $row->id }}][{{ $col->id }}]" value="1">
                                        @elseif($row->id > $col->id)
                                            <!-- Disabled input for reversed inputs as we will calculate the inv automatically on server or client, but to match prompt 'otomatis', we just show them readonly and grayed out, or empty. The prompt says 'otomatis', meaning input is only on one diagonal. So let's just make id > id readonly -->
                                            <td class="bg-light"><em class="text-muted">Otomatis</em></td>
                                        @else
                                            <td>
                                                <select name="comparisons[{{ $row->id }}][{{ $col->id }}]" class="form-select text-center form-select-sm">
                                                    @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $i)
                                                        <option value="{{ $i }}" {{ $val == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endforeach
                                                    @foreach([1/2, 1/3, 1/4, 1/5, 1/6, 1/7, 1/8, 1/9] as $i)
                                                        @php 
                                                            $str_i = explode('/', '1/'.round(1/$i))[1];
                                                            $label = '1/'.$str_i; 
                                                        @endphp
                                                        <option value="{{ $i }}" {{ abs($val - $i) < 0.001 ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-calculator"></i> Hitung Bobot AHP
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
