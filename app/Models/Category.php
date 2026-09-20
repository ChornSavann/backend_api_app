<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';
    protected $appends = ['image_url'];
    protected $fillable = [
        'name',
        'description',
        'image',
    ];
    // protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // ប្រសិនបើវាជា URL ពេញស្រាប់ (ឧ. ផ្ញើមកពីที่ផ្សេង)
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }


        return asset($this->image);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
