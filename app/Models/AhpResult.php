<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'criteria_id',
        'score',
        'weighted_score',
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'weighted_score' => 'decimal:4',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function criterion()
    {
        return $this->belongsTo(AhpCriterion::class, 'criteria_id');
    }
}
