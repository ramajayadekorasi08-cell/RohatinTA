<?php

$basePath = __DIR__;

// Helper to create directory
function ensureDir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

// Data
$modules = [
    'Admin\ComplaintController' => [
        'methods' => ['index'],
        'views' => ['admin.complaints.index' => 'Manajemen Semua Pengaduan']
    ],
    'Admin\AhpController' => [
        'methods' => ['index'],
        'views' => ['admin.ahp.index' => 'Hitung AHP']
    ],
    'Admin\CategoryController' => [
        'methods' => ['index'],
        'views' => ['admin.categories.index' => 'Kategori Pengaduan']
    ],
    'Admin\UserController' => [
        'methods' => ['index'],
        'views' => ['admin.users.index' => 'Data Pengguna']
    ],
    'Parent\ComplaintController' => [
        'methods' => ['index', 'create'],
        'views' => [
            'parent.complaints.index' => 'Riwayat Pengaduan',
            'parent.complaints.create' => 'Buat Tiket Baru'
        ]
    ],
    'Principal\ReportController' => [
        'methods' => ['complaints', 'evaluation'],
        'views' => [
            'principal.reports.complaints' => 'Laporan Pengaduan',
            'principal.reports.evaluation' => 'Laporan Evaluasi'
        ]
    ],
];

foreach ($modules as $controllerPath => $data) {
    $parts = explode('\\', $controllerPath);
    $role = strtolower($parts[0]);
    $controllerFileName = $basePath . '/app/Http/Controllers/' . str_replace('\\', '/', $controllerPath) . '.php';
    
    // Modify controller methods
    $methodsCode = "";
    foreach ($data['methods'] as $method) {
        // find out which view it corresponds to
        $viewName = "";
        foreach ($data['views'] as $v => $title) {
            if (strpos($v, $method) !== false || (strpos($v, 'complaints') !== false && $method == 'complaints') || (strpos($v, 'evaluation') !== false && $method == 'evaluation')) {
                $viewName = $v;
                break;
            }
            if ($method === 'index' && strpos($v, 'index') !== false) {
                $viewName = $v;
            }
        }
        
        $methodsCode .= "
    public function $method()
    {
        return view('$viewName');
    }
";
    }
    
    $content = file_get_contents($controllerFileName);
    $content = preg_replace('/class [a-zA-Z0-9_]+ extends Controller\s*\{\s*/', '$0' . ltrim($methodsCode), $content);
    file_put_contents($controllerFileName, $content);

    // Create views
    foreach ($data['views'] as $viewDot => $title) {
        $viewPath = $basePath . '/resources/views/' . str_replace('.', '/', $viewDot) . '.blade.php';
        ensureDir(dirname($viewPath));
        
        // generate a basic view that extends app and copies the same sidebar from the dashboard.
        // Reading the dashboard to copy its sidebar
        $dashboardPath = $basePath . '/resources/views/' . $role . '/dashboard.blade.php';
        $dashboardContent = '';
        if (file_exists($dashboardPath)) {
            $dashboardContent = file_get_contents($dashboardPath);
        }
        
        // Extract sidebar using a simple regex
        $sidebar = "";
        if (preg_match('/@section\(\'sidebar\'\)(.*?)@endsection/s', $dashboardContent, $matches)) {
            $sidebar = "@section('sidebar')\n" . trim($matches[1]) . "\n@endsection";
            // remove active class from dashboard and put it somewhere else... actually it's fine
            $sidebar = str_replace('class="nav-link active"', 'class="nav-link"', $sidebar);
        } else {
            $sidebar = "@section('sidebar')\n@endsection";
        }
        
        $breadcrumbRole = ucfirst($role);
        if ($role === 'parent') $breadcrumbRole = 'Orang Tua';
        if ($role === 'principal') $breadcrumbRole = 'Kepala Sekolah';

        $bladeContent = "@extends('layouts.app')

@section('title', '$title')
@section('page-title', '$title')

@section('breadcrumb')
    <a href=\"#\">$breadcrumbRole</a>
    <i class=\"bi bi-chevron-right\" style=\"font-size: 0.6rem\"></i>
    <span>$title</span>
@endsection

$sidebar

@section('content')
<div class=\"empty-state\">
    <i class=\"bi bi-tools\"></i>
    <h6>Hanya Dummy Halaman - $title</h6>
    <p>Halaman ini sengaja diaktifkan agar tidak muncul error saat diklik.</p>
</div>
@endsection
";
        file_put_contents($viewPath, $bladeContent);
    }
}

echo "Done.";
