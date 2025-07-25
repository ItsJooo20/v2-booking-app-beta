<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'can_be_addon',
        'can_have_addon',
        'category_id',
        'image_path',
        'total_items',
        'available_items',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'available_items' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    public function items()
    {
        return $this->hasMany(FacilityItem::class);
    }

    public function recalculateCounts()
    {
        $this->total_items = $this->items()->count();
        $this->available_items = $this->items()->where('status', 'available')->count();
        $this->save();
    }
    
    public function getImageUrl()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return asset('images/placeholder-facility.jpg');
    }
}