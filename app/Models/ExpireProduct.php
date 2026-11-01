<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpireProduct extends Model
{
    use HasFactory;

    protected $table = 'expire_product';

    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity',
        'start_date',
        'expire_date',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}