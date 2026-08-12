<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $students = $user->students; // Asumsi relation `students` sudah ada (User hasMany Student)

        if ($students->isEmpty()) {
            // Jika tidak punya anak sama sekali
            return view('parent.dashboard', [
                'stats' => ['total' => 0, 'pending' => 0, 'on_progress' => 0, 'resolved' => 0],
                'recentComplaints' => collect(),
                'students' => $students,
                'activeStudentId' => null,
                'activeStudent' => null
            ]);
        }

        // Ambil session active student id
        $activeStudentId = session('active_student_id');
        
        // Pengecekan keamanan: Pastikan activeStudentId memang benar anak milik parent ini
        if (!$activeStudentId || !$students->contains('id', $activeStudentId)) {
            $activeStudentId = $students->first()->id;
            session(['active_student_id' => $activeStudentId]);
        }

        $activeStudent = $students->firstWhere('id', $activeStudentId);

        $complaints = Complaint::where('parent_id', $user->id)
                                ->where('student_id', $activeStudentId);

        $stats = [
            'total' => $complaints->count(),
            'pending' => (clone $complaints)->where('status', 'pending')->count(),
            'on_progress' => (clone $complaints)->whereIn('status', ['approved', 'on_progress'])->count(),
            'resolved' => (clone $complaints)->where('status', 'resolved')->count(),
        ];

        $recentComplaints = (clone $complaints)
            ->with(['category', 'student'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('parent.dashboard', compact('stats', 'recentComplaints', 'students', 'activeStudentId', 'activeStudent'));
    }

    public function switchStudent($id)
    {
        $user = auth()->user();
        
        // Validasi, apakah array of student_ids milik auth()->user() mengandung $id ini.
        if ($user->students->contains('id', $id)) {
            session(['active_student_id' => $id]);
            return redirect()->route('parent.dashboard')->with('success', 'Berhasil beralih tampilan anak.');
        }

        // Jika dimanipulasi URL untuk melihat siswa orang lain
        return redirect()->route('parent.dashboard')->with('error', 'Anda tidak memiliki akses terhadap data siswa tersebut.');
    }
}
