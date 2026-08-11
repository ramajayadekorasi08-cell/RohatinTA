<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = \App\Models\Complaint::with(['parentUser', 'student', 'category', 'ahpResults'])->latest()->paginate(10);
        $criteria = \App\Models\AhpCriterion::all();

        return view('admin.complaints.index', compact('complaints', 'criteria'));
    }

    public function storeAhpResult(Request $request, $id)
    {
        $request->validate([
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|min:1|max:100',
        ]);

        $complaint = \App\Models\Complaint::findOrFail($id);

        foreach ($request->criteria as $criterionId => $score) {
            \App\Models\AhpResult::updateOrCreate(
                ['complaint_id' => $complaint->id, 'criteria_id' => $criterionId],
                ['score' => $score]
            );
        }

        return back()->with('success', 'Nilai AHP berhasil disimpan.');
    }
}
