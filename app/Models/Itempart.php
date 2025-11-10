<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itempart extends Model
{
    protected $table = 'parts';

    protected $fillable = [
        'partname',
        'partno',
        'hscode',
        'price',
        'unit',
        'note',
        'image',
        'availability',
        'customername'
    ];
}
