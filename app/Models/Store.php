<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use hasFactory;
    protected $table = "stores";
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'description',
    ];

    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }

        // ប្រសិនបើវាជា URL ពេញស្រាប់ (ឧ. ផ្ញើមកពីที่ផ្សេង)
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        return asset($this->logo);
    }

}
