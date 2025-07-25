<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityCategory extends Model
{
    use HasFactory;

    protected $table = 'facility_categories';

    protected $fillable = [ 
        'name',
        'description',
        'image_path',
        'requires_return',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'category_id');
    }

    public function getImageUrl()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return asset('images/placeholder-category.jpg');
    }
}