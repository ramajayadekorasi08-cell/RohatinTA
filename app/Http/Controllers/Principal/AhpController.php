<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    /**
     * Display a ranking of complaints based on AHP priority score.
     */
    public function index()
    {
        // Get non-pending complaints (approved, on_progress, resolved) 
        // Order by priority_score descending
        $complaints = Complaint::with(['parentUser', 'student', 'category'])
            ->whereNotIn('status', [Complaint::STATUS_PENDING, Complaint::STATUS_REJECTED])
            ->whereNotNull('priority_score')
            ->orderByDesc('priority_score')
            ->paginate(15);

        return view('principal.ahp', compact('complaints'));
    }
}
