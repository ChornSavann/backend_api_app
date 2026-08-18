<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments'; 
    protected $fillable = [
        'order_id', 
        'payment_method', 
        'amount', 
        'change_amount', 
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}