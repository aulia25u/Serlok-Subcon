<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    use HasFactory;

    protected $table = 'depts';

    protected $fillable = [
        'dept_name',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}

