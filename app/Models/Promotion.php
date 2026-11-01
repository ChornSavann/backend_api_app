<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'product_promotions';

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'promotion_code',
        'quantity',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $appends = ['final_price', 'discount_amount'];

    public function getFinalPriceAttribute()
    {
        if (!$this->product) {
            return 0;
        }

        $sellingPrice = $this->product->selling_price; 
        $discountValue = $this->discount_value;

        if ($this->discount_type == 'percentage') {
            $discountAmount = ($sellingPrice * $discountValue) / 100;
            $finalPrice = $sellingPrice - $discountAmount;
        } else {
            $finalPrice = $sellingPrice - $discountValue;
        }

        return round(max(0, $finalPrice), 2);
    }

    public function getDiscountAmountAttribute()
    {
        if (!$this->product) {
            return 0;
        }

        $sellingPrice = $this->product->selling_price;
        if ($this->discount_type == 'percentage') {
            return round(($sellingPrice * $this->discount_value) / 100, 2);
        } else {
            return round($this->discount_value, 2);
        }
    }
}