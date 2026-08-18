<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Units extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'short_name',
        'base_unit_id',
        'operator',
        'value',
    ];

    // ទំនាក់ទំនង៖ ខ្នាតធំអាចទាក់ទងទៅខ្នាតមូលដ្ឋាន (Base Unit)
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    // ទំនាក់ទំនង៖ ខ្នាតមួយអាចមានខ្នាតកូនៗផ្សេងទៀតយោងមកលើវា
    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    // ទំនាក់ទំនង៖ Unit មួយអាចប្រើប្រាស់ក្នុង Products ច្រើន
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
