<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $fillable = [
        'name',
        'company',    
        'contact_name',
        'phone',
        'email',
        'website',    
        'address',
        'is_active',
    ];


    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
