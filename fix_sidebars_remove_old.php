<?php
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
    $content = file_get_contents($file);
    
    // We regex replace the old Hitung AHP block
    // <a href="{{ route('admin.ahp.index') }}" class="nav-link...">
    //     <i class="bi bi-diagram-3"></i>
    //     <span>Hitung AHP</span>
    // </a>
    $pattern = '/<a href="\{\{ route\(\'admin\.ahp\.index\'\) \}\}".*?>\s*<i.*?><\/i>\s*<span>Hitung AHP<\/span>\s*<\/a>/s';
    
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, '', $content);
        file_put_contents($file, $content);
        echo "Removed old 'Hitung AHP' link in $file\n";
    }
}
echo "Done\n";
