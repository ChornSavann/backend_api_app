<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;
class ExpenseType extends Model
{
    protected $table = "expense_types";
    protected $fillable = ['name', 'description'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_type_id');
    }
}