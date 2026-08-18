<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'points',
        'address',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
