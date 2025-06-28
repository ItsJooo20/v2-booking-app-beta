<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_item_id',
        'image_path',
        'is_primary',
        'display_order'
    ];

    public function facilityItem()
    {
        return $this->belongsTo(FacilityItem::class);
    }

    public function getImageUrl()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return asset('images/placeholder-item.jpg');
    }
}