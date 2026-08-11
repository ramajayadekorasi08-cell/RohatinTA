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
    public function index()
    {
        $complaints = Complaint::where('parent_id', auth()->user()->id)
            ->with(['category', 'student', 'rating'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('parent.complaints.index', compact('complaints'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        // Option to pick a student; ideally filtered by parent_id if there's a relation, but typically system just lists them if no relation exists in Student
        // If Student has parent_id, then ->where('parent_id', auth()->id())
        $students = Student::all(); 

        return view('parent.complaints.create', compact('categories', 'students'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'student_id' => 'required|exists:students,id',
            'description' => 'required|string|min:10',
        ]);
        
        $trackingCode = 'ADU-' . date('Y') . '-' . strtoupper(Str::random(6));

        Complaint::create([
            'parent_id' => auth()->id(),
            'category_id' => $request->category_id,
            'student_id' => $request->student_id,
            'description' => $request->description,
            'tracking_code' => $trackingCode,
            'status' => 'pending',
            // Default priority fields to allow AHP to work later
            'priority_level' => 'low', 
            'priority_score' => 0.00,
        ]);

        return redirect()->route('parent.complaints.index')->with('success', 'Tiket pengaduan berhasil dibuat dengan nomor tiket: ' . $trackingCode);
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
