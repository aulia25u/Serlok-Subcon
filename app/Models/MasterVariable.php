<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'variable_code',
        'variable_name',
        'variable_value',
        'description',
    ];
}
