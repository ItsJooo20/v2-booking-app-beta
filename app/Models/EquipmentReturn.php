<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentReturn extends Model
{
    use HasFactory;

    protected $table = 'equipment_returns';

    protected $fillable = [
        'booking_id',
        'return_date',
        'return_photo_path',
        'user_condition',
        'condition_status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}