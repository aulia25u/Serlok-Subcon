<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dept;
use App\Models\Section;
use App\Models\Position;
use App\Models\Role;
use App\Models\Plant;
use App\Models\UserDetail;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_code',
        'contact_person',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departments()
    {
        return $this->hasMany(Dept::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}
