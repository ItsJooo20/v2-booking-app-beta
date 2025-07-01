<?php

namespace App\Models;

use App\Models\EquipmentReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_item_id',
        'start_datetime',
        'end_datetime',
        'purpose',
        'status',
        // 'headmaster_approved',
    ];

    protected $casts = [
        // 'headmaster_approved' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function equipmentRequests()
    {
        return $this->hasMany(BookingEquipmentRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facilityItem()
    {
        return $this->belongsTo(FacilityItem::class, 'facility_item_id');
    }

    public function equipmentReturn()
    {
        return $this->hasOne(EquipmentReturn::class, 'booking_id');
    }    

    public function isActive()
    {
        $now = now();
        return $this->start_datetime <= $now && $this->end_datetime >= $now && $this->status === 'approved';
    }
}