<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::with('parent')->latest();

        // Search logic
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('parent', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Filter by Class logic
        if ($request->has('class_filter') && $request->class_filter != '') {
            $query->where('class', $request->class_filter);
        }

        $students = $query->paginate(15)->withQueryString();
        
        $classes = Student::select('class')->distinct()->pluck('class');

        return view('admin.students.index', compact('students', 'classes'));
    }

    /**
     * Store a newly created student in storage (and auto generate Parent account).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:5|regex:/^[0-9]{1,5}$/|unique:students,nis',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'class' => 'required|string|max:100',
            'tahun_masuk' => 'required|integer|min:2000',
            'address' => 'nullable|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'parent_email' => 'nullable|email',
        ]);

        try {
            DB::beginTransaction();
            $msg = '';
            
            // 1. Cari Akun Orang Tua berdasarkan No WhatsApp 
            $parentUser = User::where('phone', $request->parent_phone)
                ->where('role', 'parent')
                ->first();
                
            if (!$parentUser && $request->filled('parent_email')) {
                $parentUser = User::where('email', $request->parent_email)
                    ->where('role', 'parent')
                    ->first();
            }

            if ($parentUser) {
                // Akun sudah ada, gunakan yang ada
                $msg = "Siswa {$request->name} berhasil ditambahkan ke akun Orang Tua yang sudah ada: {$parentUser->username}.";
            } else {
                // Buat akun baru
                $baseUsername = Str::slug($request->parent_name, '');
                $username = $baseUsername;
                $counter = 2;
                while(User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $rawPassword = 'sipadu' . $request->tahun_masuk;

                $parentUser = User::create([
                    'name' => $request->parent_name,
                    'username' => $username,
                    'email' => $request->parent_email,
                    'phone' => $request->parent_phone,
                    'password' => Hash::make($rawPassword),
                    'role' => 'parent',
                    'is_active' => true,
                ]);

                $msg = "Siswa {$request->name} berhasil ditambahkan. Akun Orang Tua berhasil dibuat (Username: {$username} / Password: {$rawPassword})";
            }

            // 2. Create Student relation
            Student::create([
                'nis' => $request->nis,
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'class' => $request->class,
                'tahun_masuk' => $request->tahun_masuk,
                'address' => $request->address,
                'parent_id' => $parentUser->id,
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat data siswa dan orang tua: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')->withInput();
        }
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => ['required', 'string', 'max:5', 'regex:/^[0-9]{1,5}$/', Rule::unique('students', 'nis')->ignore($student->id)],
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'class' => 'required|string|max:100',
            'tahun_masuk' => 'required|integer|min:2000',
            'address' => 'nullable|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'parent_email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($student->parent_id)],
        ]);

        try {
            DB::beginTransaction();

            $student->update([
                'nis' => $request->nis,
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'class' => $request->class,
                'tahun_masuk' => $request->tahun_masuk,
                'address' => $request->address,
            ]);

            if ($student->parent) {
                $student->parent->update([
                    'name' => $request->parent_name,
                    'phone' => $request->parent_phone,
                    'email' => $request->parent_email,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal update data siswa: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengupdate data.')->withInput();
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();

            $parentId = $student->parent_id;
            $student->delete();
            
            // Hapus akun ortu HANYA jika ia tidak memiliki anak lagi
            if ($parentId) {
                $parentUser = User::find($parentId);
                if ($parentUser && $parentUser->students()->count() == 0) {
                    $parentUser->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.students.index')->with('success', 'Data Siswa berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal hapus data siswa: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
    }
}
