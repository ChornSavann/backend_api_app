<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    // ទំនាក់ទំនង៖ ការទិញមួយ (Purchase) អាចមានទំនិញច្រើន (PurchaseItems)
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
