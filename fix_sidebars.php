<?php
$html = '    <div class="nav-label">Pengaturan AHP</div>
    <a href="{{ route(\'admin.ahp.index\') }}" class="nav-link {{ request()->routeIs(\'admin.ahp.index\') ? \'active\' : \'\' }}">
        <i class="bi bi-diagram-3"></i>
        <span>Hasil AHP & Bobot</span>
    </a>
    <a href="{{ route(\'admin.ahp.comparison\') }}" class="nav-link {{ request()->routeIs(\'admin.ahp.comparison\') ? \'active\' : \'\' }}">
        <i class="bi bi-table"></i>
        <span>Perbandingan Kriteria</span>
    </a>
    
    <div class="nav-label">Master Data</div>';

function getFiles($dir) {
    $files = [];
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    foreach ($iter as $path => $dir) {
        if ($dir->isFile() && substr($path, -10) === '.blade.php') {
            $files[] = $path;
        }
    }
    return $files;
}

$files = getFiles('resources/views/admin');

foreach ($files as $file) {
    if (strpos($file, 'comparison.blade.php') !== false) continue;

    $content = file_get_contents($file);
    
    if (strpos($content, '<div class="nav-label">Master Data</div>') !== false && strpos($content, 'Pengaturan AHP') === false) {
        $content = str_replace('<div class="nav-label">Master Data</div>', $html, $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
echo "Done\n";
