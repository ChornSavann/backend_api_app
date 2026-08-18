<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';
    protected $fillable = [
        'name',
        'description',
    ];

    // កំណត់ថា Category មួយអាចមាន Product ច្រើន (Has Many)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
