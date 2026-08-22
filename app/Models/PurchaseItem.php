<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_items';
    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_name',
        'unit_cost',
        'quantity',
        'total_price',
    ];


    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id'); 
        
    }
}
