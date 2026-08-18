<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;
use App\Models\Product;

class OrderDetail extends Model
{
    protected $table = 'order_details'; 
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 
        'unit_price', 'quantity', 'total_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}