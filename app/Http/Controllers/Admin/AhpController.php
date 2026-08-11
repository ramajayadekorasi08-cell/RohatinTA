<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function index()
    {
        $criteria = \App\Models\AhpCriterion::all();
        $complaints = \App\Models\Complaint::with(['ahpResults', 'parentUser', 'student', 'category'])
                        ->whereHas('ahpResults')
                        ->orderBy('priority_score', 'desc')
                        ->get();

        return view('admin.ahp.index', compact('criteria', 'complaints'));
    }

    public function calculate()
    {
        $criteria = \App\Models\AhpCriterion::all();
        $complaints = \App\Models\Complaint::with('ahpResults')->whereHas('ahpResults')->get();

        foreach ($complaints as $complaint) {
            $totalScore = 0;
            
            foreach ($criteria as $criterion) {
                $result = $complaint->ahpResults->where('criteria_id', $criterion->id)->first();
                if ($result) {
                    // Normalization: score / 100 * weight
                    $weightedScore = ($result->score / 100) * $criterion->weight;
                    $result->weighted_score = $weightedScore;
                    $result->save();
                    
                    $totalScore += $weightedScore;
                }
            }

            $priorityLevel = 'low';
            if ($totalScore >= 0.70) {
                $priorityLevel = 'high';
            } elseif ($totalScore >= 0.50) {
                $priorityLevel = 'medium';
            }

            $complaint->priority_score = $totalScore;
            $complaint->priority_level = $priorityLevel;
            $complaint->save();
        }

        return back()->with('success', 'Perhitungan AHP berhasil diselesaikan. Skor prioritas telah diperbarui.');
    }
}
