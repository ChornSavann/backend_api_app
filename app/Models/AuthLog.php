<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;
    protected $table = 'auth_logs';
    protected $fillable = [
        'user_id',
        'action',
        'timestamp',
    ];

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
