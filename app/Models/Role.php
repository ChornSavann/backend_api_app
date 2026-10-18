<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Permission;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description'];

    // ទំនាក់ទំនង៖ 1 Role អាចមាន User ច្រើន
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class, 
            'permission_role', 
            'role_id',         
            'permission_id'   
        )->withTimestamps();   
    }
}