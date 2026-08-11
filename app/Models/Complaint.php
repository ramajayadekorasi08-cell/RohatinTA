<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'parent_id',
        'student_id',
        'category_id',
        'title',
        'description',
        'evidence_path',
        'status',
        'priority_score',
        'priority_level',
        'assigned_to',
        'rejection_reason',
        'resolution_note',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'priority_score' => 'decimal:4',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ON_PROGRESS = 'on_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_REJECTED = 'rejected';

    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    public static function generateTrackingCode(): string
    {
        $year = date('Y');
        $lastComplaint = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastComplaint ? ((int) substr($lastComplaint->tracking_code, -5)) + 1 : 1;

        return 'ADU-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_APPROVED => 'Diterima & Diteruskan',
            self::STATUS_ON_PROGRESS => 'Sedang Ditindaklanjuti',
            self::STATUS_RESOLVED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_ON_PROGRESS => 'primary',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority_level) {
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_LOW => 'success',
            default => 'secondary',
        };
    }

    // Relationships
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ComplaintStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function assignments()
    {
        return $this->hasMany(ComplaintAssignment::class);
    }

    public function latestAssignment()
    {
        return $this->hasOne(ComplaintAssignment::class)->latestOfMany();
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class);
    }
}
