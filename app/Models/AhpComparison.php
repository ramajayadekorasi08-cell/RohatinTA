<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'criteria_row_id',
        'criteria_col_id',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:4',
    ];

    public function criteriaRow()
    {
        return $this->belongsTo(AhpCriterion::class, 'criteria_row_id');
    }

    public function criteriaCol()
    {
        return $this->belongsTo(AhpCriterion::class, 'criteria_col_id');
    }
}
