<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityItem extends Model
{
    use HasFactory;

    protected $table = 'facility_items';

    public $timestamps = false;

    protected $fillable = [
        'facility_id',
        'item_code',
        'notes',
    ];

    protected $casts = [
        'is_borrowed' => 'boolean',
    ];
    
    public function facilityItemImage()
    {
        return $this->hasOne(FacilityItemImage::class, 'facility_item_id')
            ->where('is_primary', 1);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'facility_item_id');
    }

    public function equipmentRequests()
    {
        return $this->hasMany(BookingEquipmentRequest::class, 'facility_item_id');
    }

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class, 'facility_item_id');
    }

    public function images()
    {
        return $this->hasMany(FacilityItemImage::class)
            ->orderByRaw('is_primary DESC')
            ->orderBy('display_order')
            ->orderBy('id');
    }

    public function getPrimaryImage()
    {
        $primaryImage = $this->images()->where('is_primary', true)->first();
        
        if (!$primaryImage) {
            $primaryImage = $this->images()->first();
        }
        
        return $primaryImage;
    }
    
    public function getPrimaryImageUrl()
    {
        $primaryImage = $this->getPrimaryImage();
        
        if ($primaryImage) {
            return $primaryImage->getImageUrl();
        }
        
        return asset('images/placeholder-item.jpg');
    }
    
    public function getAllImageUrls()
    {
        return $this->images->map(function($image) {
            return [
                'id' => $image->id,
                'url' => $image->getImageUrl(),
                'is_primary' => $image->is_primary
            ];
        });
    }
}