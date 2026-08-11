<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpCriterion extends Model
{
    use HasFactory;

    protected $table = 'ahp_criteria';

    protected $fillable = [
        'name',
        'code',
        'description',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
    ];

    public function comparisonsAsRow()
    {
        return $this->hasMany(AhpComparison::class, 'criteria_row_id');
    }

    public function comparisonsAsCol()
    {
        return $this->hasMany(AhpComparison::class, 'criteria_col_id');
    }

    public function results()
    {
        return $this->hasMany(AhpResult::class, 'criteria_id');
    }
}
