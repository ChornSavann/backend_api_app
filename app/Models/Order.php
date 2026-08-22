<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Invoice;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'order_number', 'customer_id', 'user_id', 
        'subtotal', 'discount_amount', 'tax_amount', 
        'total_amount', 'status'
    ];

    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id');
    }
}
