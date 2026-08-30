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

    public function comparison()
    {
        $criteria = \App\Models\AhpCriterion::all();
        $comparisons = \App\Models\AhpComparison::all();
        
        return view('admin.ahp.comparison', compact('criteria', 'comparisons'));
    }

    public function storeComparison(Request $request)
    {
        $input = $request->input('comparisons');
        if (!$input) return back()->with('error', 'Tidak ada data input.');

        $criteria = \App\Models\AhpCriterion::all();
        $n = $criteria->count();

        // Save comparisons to DB
        foreach ($input as $row_id => $cols) {
            foreach ($cols as $col_id => $val) {
                // Save normal value
                \App\Models\AhpComparison::updateOrCreate(
                    ['criteria_row_id' => $row_id, 'criteria_col_id' => $col_id],
                    ['value' => $val]
                );
                // Automatically save inverse
                if ($row_id != $col_id) {
                    \App\Models\AhpComparison::updateOrCreate(
                        ['criteria_row_id' => $col_id, 'criteria_col_id' => $row_id],
                        ['value' => 1 / $val]
                    );
                }
            }
        }

        // Calculation starts
        $matrix = [];
        $colSum = [];
        $criteriaIds = $criteria->pluck('id')->toArray();

        foreach ($criteriaIds as $r) {
            foreach ($criteriaIds as $c) {
                $comp = \App\Models\AhpComparison::where('criteria_row_id', $r)
                                                 ->where('criteria_col_id', $c)->first();
                $val = $comp ? $comp->value : 1;
                $matrix[$r][$c] = $val;
                if (!isset($colSum[$c])) $colSum[$c] = 0;
                $colSum[$c] += $val;
            }
        }

        // Normalize and get Weights (Eigen Vector)
        $weightSum = [];
        foreach ($criteriaIds as $r) {
            $sum = 0;
            foreach ($criteriaIds as $c) {
                $norm = $matrix[$r][$c] / $colSum[$c];
                $sum += $norm;
            }
            $weight = $sum / $n;
            $weightSum[$r] = $weight;

            $criterion = \App\Models\AhpCriterion::find($r);
            $criterion->weight = $weight;
            $criterion->save();
        }

        // Predict Lambda Max
        $lambdaMax = 0;
        foreach ($criteriaIds as $r) {
            $calcSum = 0;
            foreach ($criteriaIds as $c) {
                $calcSum += $matrix[$r][$c] * $weightSum[$c];
            }
            $lambdaMax += $calcSum / $weightSum[$r];
        }
        $lambdaMax = $lambdaMax / $n;

        // CI and CR
        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri_dict = [1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
        $ri = $ri_dict[$n] ?? 1.49; 
        $cr = $ri == 0 ? 0 : $ci / $ri;

        if ($cr <= 0.1) {
            return back()->with('success', 'Perhitungan AHP Konsisten! CR: '.round($cr, 3).'. Bobot berhasil disimpan.');
        } else {
            return back()->with('warning', 'Perhatian! Perhitungan AHP TIDAK Konsisten (CR: '.round($cr, 3).' > 0.1). Mohon perbaiki rasio nilai Anda.');
        }
    }
}
