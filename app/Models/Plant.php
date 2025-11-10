<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $table = 'plants';

    protected $fillable = [
        'plant_name',
    ];

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}

