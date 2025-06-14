<?php

namespace App\Models;

use App\Mail\VerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 11 feature - auto hash password
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship dengan booking
     */
    public function bookings()
    {
        // return $this->hasMany(Booking::class);
    }

    /**
     * Relationship dengan damage reports
     */
    public function damageReports()
    {
        // return $this->hasMany(DamageReport::class, 'reporter_id');
    }

    /**
     * Relationship dengan repair tasks
     */
    public function repairTasks()
    {
        // return $this->hasMany(RepairTask::class, 'technician_id');
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check apakah user adalah technician
     */
    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    /**
     * Check apakah user adalah headmaster
     */
    public function isHeadmaster(): bool
    {
        return $this->role === 'headmaster';
    }

    /**
     * Check apakah user adalah regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get user's full name with role
     */
    public function getFullNameWithRoleAttribute(): string
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }

    /**
     * Override default email verification method untuk custom URL
     */
    public function sendEmailVerificationNotification()
    {
        // Bisa customize notification email di sini jika perlu
        // Mail::to($this->email)->send(new VerifyEmail($this, $this->verificationUrl));
        $this->notify(new \App\Notifications\VerifyEmail);
        // $this->notify(new CustomVerifyEmail);
        // $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }
}