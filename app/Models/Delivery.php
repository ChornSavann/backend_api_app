<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';
    protected $fillable = [
        'order_id',
        'pickup_address',
        'delivery_address',
        'delivery_fee',
        'delivery_partner',
        'receiver_name',
        'receiver_phone',
        'note',
        'status',
    ];

    // 🔗 ទំនាក់ទំនង៖ Delivery មួយជាកម្មសិទ្ធិរបស់ Order មួយ
   public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}