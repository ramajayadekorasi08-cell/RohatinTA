<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role helpers
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPrincipal(): bool
    {
        return $this->role === 'principal';
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'parent_id');
    }

    public function inAppNotifications()
    {
        return $this->hasMany(InAppNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->inAppNotifications()->where('is_read', false);
    }
}
