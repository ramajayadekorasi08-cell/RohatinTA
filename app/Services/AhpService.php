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
            $criteria = AhpCriterion::all()->keyBy('code');
            
            if ($criteria->isEmpty()) {
                throw new \Exception("AHP Criteria not configured in database.");
            }

            $urgencyScore = $this->calculateUrgency($complaint);
            $impactScore = $this->calculateImpact($complaint);
            $timeScore = $this->calculateTime($complaint);

            // Store AHP results per criteria (if needed for tracking)
            // URG = urgency, DMP = impact, WKT = time
            $this->storeResult($complaint, $criteria->get('URG'), $urgencyScore);
            $this->storeResult($complaint, $criteria->get('DMP'), $impactScore);
            $this->storeResult($complaint, $criteria->get('WKT'), $timeScore);

            // Calculate final priority score using criteria weights
            // Normalizing the scores to 0-1 range if weights are 0-1, or standardizing.
            // Assuming weights sum to 1.
            $urgencyWeight = $criteria->get('URG') ? $criteria->get('URG')->weight : (1/3);
            $impactWeight = $criteria->get('DMP') ? $criteria->get('DMP')->weight : (1/3);
            $timeWeight = $criteria->get('WKT') ? $criteria->get('WKT')->weight : (1/3);

            // Scores are 0-100, we can divide by 100 to get a 0-1 bounded priority score
            $finalScore = (
                ($urgencyScore * $urgencyWeight) +
                ($impactScore * $impactWeight) +
                ($timeScore * $timeWeight)
            ) / 100;

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

    /**
     * Calculate Urgency score based on description keywords
     */
    protected function calculateUrgency(Complaint $complaint): int
    {
        $text = strtolower($complaint->title . ' ' . $complaint->description);
        
        if (str_contains($text, 'bullying') || str_contains($text, 'perundungan') || str_contains($text, 'kekerasan')) {
            return 100;
        }
        if (str_contains($text, 'sakit') || str_contains($text, 'medis')) {
            return 80;
        }
        if (str_contains($text, 'rusak') || str_contains($text, 'bocor') || str_contains($text, 'bahaya')) {
            return 60;
        }
        if (str_contains($text, 'kotor') || str_contains($text, 'sampah')) {
            return 40;
        }
        
        return 50; // Default
    }

    /**
     * Calculate Impact score based on Category
     */
    protected function calculateImpact(Complaint $complaint): int
    {
        $categoryName = strtolower($complaint->category->name ?? '');
        
        if (str_contains($categoryName, 'non-akademik') || str_contains($categoryName, 'kesiswaan')) {
            return 100;
        }
        if (str_contains($categoryName, 'akademik') || str_contains($categoryName, 'pembelajaran')) {
            return 70;
        }
        if (str_contains($categoryName, 'sarana') || str_contains($categoryName, 'fasilitas')) {
            return 50;
        }
        
        return 60; // Default
    }

    /**
     * Calculate Time score based on days since creation
     */
    protected function calculateTime(Complaint $complaint): int
    {
        if (!$complaint->created_at) {
            return 40;
        }

        $days = now()->diffInDays($complaint->created_at);
        
        if ($days > 7) {
            return 100;
        }
        if ($days >= 4) {
            return 70;
        }
        
        return 40; // Default for 0-3 days
    }

    /**
     * Store individual criterion result
     */
    protected function storeResult(Complaint $complaint, ?AhpCriterion $criterion, int $score): void
    {
        if (!$criterion) return;

        AhpResult::updateOrCreate(
            [
                'complaint_id' => $complaint->id,
                'ahp_criterion_id' => $criterion->id,
            ],
            [
                'score' => $score,
            ]
        );
    }
}
