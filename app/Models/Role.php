<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'role_name',
        'customer_id',
    ];

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }

    public function roleToMenus()
    {
        return $this->hasMany(RoleToMenu::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
