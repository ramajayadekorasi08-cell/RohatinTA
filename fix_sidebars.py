import os
import glob

html = """    <div class="nav-label">Pengaturan AHP</div>
    <a href="{{ route('admin.ahp.index') }}" class="nav-link {{ request()->routeIs('admin.ahp.index') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i>
        <span>Hasil AHP & Bobot</span>
    </a>
    <a href="{{ route('admin.ahp.comparison') }}" class="nav-link {{ request()->routeIs('admin.ahp.comparison') ? 'active' : '' }}">
        <i class="bi bi-table"></i>
        <span>Perbandingan Kriteria</span>
    </a>
    
    <div class="nav-label">Master Data</div>"""

files = glob.glob("resources/views/admin/**/*.blade.php", recursive=True)

for file in files:
    if "comparison.blade.php" in file:
        continue
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if '<div class="nav-label">Master Data</div>' in content and 'Pengaturan AHP' not in content:
        content = content.replace('<div class="nav-label">Master Data</div>', html)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {file}")

print("Done")
