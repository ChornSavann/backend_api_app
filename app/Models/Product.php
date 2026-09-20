<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'brand_id',
        'unit_id',
        'sku',
        'barcode',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'alert_quantity',
        'image',
        'description',
        'is_active',
    ];

    protected $appends = ['image_url'];

    // 🟢 បង្កើត Accessor
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return asset($this->image);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }
    protected $casts = [
        'images' => 'array',
    ];
}
