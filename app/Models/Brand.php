<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
     protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'created_at',
        'updated_at'
    ];

   
    public function getImageUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }
        return asset($this->logo);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
