<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'assigned_to_name',
        'assigned_to_role',
        'note',
        'assigned_by',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
