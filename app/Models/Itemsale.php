<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itemsale extends Model
{
    protected $table = 'itemsales';

    protected $fillable = [
        'name',
        'partname',
        'description',
        'price',
        'unit',
        'image',
        'qty',
        'operator_id',
        'availability',
        'customer_id',
        'note',
        'kode',
        'gi_id',
        'koding',
        'dateitems',
        'stokinjuli',
        'stokout',
        'harga',
        'hscode',
        'partnosales',
        'stokinjuni',
        'stokin'
    ];
}
