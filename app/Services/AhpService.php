<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\AhpCriterion;
use App\Models\AhpResult;
use Illuminate\Support\Facades\DB;

class AhpService
{
    /**
     * Calculate AHP score automatically based on complaint attributes
     *
     * @param Complaint $complaint
     * @return void
     */
    public function calculate(Complaint $complaint): void
    {
        DB::beginTransaction();
        try {
            // Get all criteria
            $criteria = AhpCriterion::all();
            
            if ($criteria->isEmpty()) {
                throw new \Exception("AHP Criteria not configured in database.");
            }

            $finalScore = 0;
            // Skala nilai adalah 1-5 (Sangat Rendah - Sangat Tinggi)
            $maxScore = 5;

            // Score dihitung dari input manual form yang disimpan Admin ke tabel ahp_results
            foreach ($criteria as $criterion) {
                // Get result for this criterion (saved during verification)
                $result = $complaint->ahpResults()->where('ahp_criterion_id', $criterion->id)->first();
                $score = $result ? $result->score : 1; 

                // Normalisasi benefit (nilai / maks)
                $normal = $score / $maxScore;
                
                // Kalikan dengan bobot hasil AHP Matriks
                $weightedScore = $normal * $criterion->weight;
                $finalScore += $weightedScore;
            }

            // Determine priority level
            $priorityLevel = 'low';
            if ($finalScore >= 0.70) {
                $priorityLevel = 'high';
            } elseif ($finalScore >= 0.50) {
                $priorityLevel = 'medium';
            }

            // Update complaint
            $complaint->update([
                'priority_score' => $finalScore,
                'priority_level' => $priorityLevel,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('AHP Calculation failed: ' . $e->getMessage());
        }
    }
}
