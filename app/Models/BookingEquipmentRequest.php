<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingEquipmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'facility_item_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function facilityItem()
    {
        return $this->belongsTo(FacilityItem::class, 'facility_item_id');
    }

     public function equipmentReturn()
    {
        return $this->hasOne(EquipmentReturn::class, 'booking_equipment_request_id');
    }

    /**
     * Or if equipment returns are linked to booking and you want to access it
     */
    public function bookingEquipmentReturn()
    {
        return $this->hasOneThrough(
            EquipmentReturn::class,
            Booking::class,
            'id', // Foreign key on bookings table
            'booking_id', // Foreign key on equipment_returns table
            'booking_id', // Local key on booking_equipment_requests table
            'id' // Local key on bookings table
        );
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for requests that need return
     */
    public function scopeNeedsReturn($query)
    {
        return $query->where('status', 'needs return');
    }

    /**
     * Check if this equipment request requires return
     */
    public function requiresReturn()
    {
        return $this->facilityItem->facility->category->requires_return ?? false;
    }
}
