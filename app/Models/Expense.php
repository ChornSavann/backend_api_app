<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExpenseType;
class Expense extends Model
{
    protected $table = "expense";
    protected $fillable = [
        'expense_type_id', 
        'user_id', 
        'amount', 
        'payment_method',
        'reference_no', 
        'expense_date', 
        'note'
    ];

    // ទំនាក់ទំនងទៅកាន់ Expense Type
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    // ទំនាក់ទំនងទៅកាន់ User (អ្នកកត់ត្រា)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
