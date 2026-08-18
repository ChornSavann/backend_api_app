<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    // បើក្នុងតារាងមិនមាន cột updated_at ទេ អាចកំណត់ timestamps = false ត្រង់នេះបាន
    public $timestamps = false; 

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}