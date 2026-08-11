<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori Pengaduan berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori Pengaduan berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Maybe check if category is used in complaints before deleting?
        if ($category->complaints()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Kategori ini tidak dapat dihapus karena sedang digunakan dalam pengaduan.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori Pengaduan berhasil dihapus.');
    }
}
