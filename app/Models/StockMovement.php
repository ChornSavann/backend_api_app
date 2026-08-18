<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements'; 
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_no',
        'note',
    ];

    // ទំនាក់ទំនងជាមួយ Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ទំនាក់ទំនងជាមួយ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}