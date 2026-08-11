<?php
$files = glob('resources/views/admin/*/*.blade.php');
foreach($files as $f) {
    if (strpos($f, 'dashboard.blade.php') !== false) continue;
    $c = file_get_contents($f);
    $c = preg_replace('/@if\(\$stats\[\'pending\'\] > 0\).*?@endif/s', '', $c);
    file_put_contents($f, $c);
}
echo "Fixed stats reference";
