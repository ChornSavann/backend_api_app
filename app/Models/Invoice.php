<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'order_id',     
        'invoice_number',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'payment_method',
        'status',
    ];

    // 🔗 Relationship: វិក្កយបត្រនេះជារបស់ Order ណា
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // 🔗 Relationship: វិក្កយបត្រមួយបង្កើតឡើងដោយ User ណាមួយ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 📦 Relationship: វិក្កយបត្រមួយអាចមានមុខទំនិញច្រើន (Invoice Items)
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}