<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\Student;
use App\Models\Rating;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Helper: Mendapatkan active student yang aman (milik parent yang login).
     */
    private function getActiveStudent()
    {
        $user = auth()->user();
        $students = $user->students;

        if ($students->isEmpty()) {
            return null;
        }

        $activeStudentId = session('active_student_id');
        if (!$activeStudentId || !$students->contains('id', $activeStudentId)) {
            $activeStudentId = $students->first()->id;
            session(['active_student_id' => $activeStudentId]);
        }

        return $students->firstWhere('id', $activeStudentId);
    }

    public function index()
    {
        $activeStudent = $this->getActiveStudent();
        $students = auth()->user()->students;

        $query = Complaint::where('parent_id', auth()->user()->id)
            ->with(['category', 'student', 'rating'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan anak yang aktif
        if ($activeStudent) {
            $query->where('student_id', $activeStudent->id);
        }

        $complaints = $query->paginate(10);
            
        return view('parent.complaints.index', compact('complaints', 'students', 'activeStudent'));
    }

    public function create()
    {
        $activeStudent = $this->getActiveStudent();

        if (!$activeStudent) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Anda belum memiliki data siswa yang terdaftar. Hubungi Admin.');
        }

        $categories = Category::where('is_active', true)->get();

        return view('parent.complaints.create', compact('categories', 'activeStudent'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:10',
        ]);

        $activeStudent = $this->getActiveStudent();

        if (!$activeStudent) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Tidak dapat membuat pengaduan. Data siswa tidak ditemukan.');
        }

        $complaint = Complaint::create([
            'parent_id'     => auth()->id(),
            'category_id'   => $request->category_id,
            'student_id'    => $activeStudent->id,
            'description'   => $request->description,
            'tracking_code' => Complaint::generateTrackingCode(),
            'status'        => 'pending',
            'priority_level'=> 'low',
            'priority_score'=> 0.00,
            'submitted_at'  => now(),
        ]);

        // Send WA notification (fail-safe) — sertakan nama siswa
        $parent = auth()->user();
        if ($parent->phone) {
            try {
                (new \App\Services\WhatsappService())->notifyComplaintCreated(
                    $parent->phone,
                    $complaint->tracking_code,
                    $parent->id,
                    $complaint->id
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WA notification failed on complaint create: ' . $e->getMessage());
            }
        }

        return redirect()->route('parent.complaints.index')
            ->with('success', 'Tiket pengaduan untuk siswa ' . $activeStudent->name . ' berhasil dibuat! No. Tiket: ' . $complaint->tracking_code);
    }
    
    public function rate(Request $request, Complaint $complaint)
    {
        // Pastikan tiket ini milik parent yang login dan sudah resolved
        if ($complaint->parent_id !== auth()->id() || $complaint->status !== 'resolved') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        Rating::updateOrCreate(
            ['complaint_id' => $complaint->id, 'user_id' => auth()->id()],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        return redirect()->back()->with('success', 'Terima kasih atas penilaian dan ulasan Anda!');
    }
}
