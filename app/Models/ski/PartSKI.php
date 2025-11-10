<?php

namespace App\Models\ski;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartSKI extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'shimada_ski';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'masterparts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partno',
        'partname',
        'coding',
        'datepart',
        'image',
        'customer_id',
        'nourut',
        'kodinginternal',
        'koding_sf',
        'availability',
        'tipe_part_no',
        'cmp_layer_satu',
        'brt_cmp_layer_satu',
        'cmp_layer_dua',
        'brt_cmp_layer_dua',
        'cmp_layer_tiga',
        'brt_cmp_layer_tiga',
        'brt_cmp_total',
        'note',
        'stock_in',
        'price',
        'koding',
        'stock_out',
        'units',
        'productshape_id',
        'productproses_id',
        'stock_awal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'nourut' => 'integer',
        'availability' => 'integer',
        'cmp_layer_satu' => 'integer',
        'brt_cmp_layer_satu' => 'double',
        'cmp_layer_dua' => 'integer',
        'brt_cmp_layer_dua' => 'double',
        'cmp_layer_tiga' => 'integer',
        'brt_cmp_layer_tiga' => 'double',
        'brt_cmp_total' => 'double',
        'stock_in' => 'integer',
        'price' => 'float',
        'stock_out' => 'integer',
        'productshape_id' => 'integer',
        'productproses_id' => 'integer',
        'stock_awal' => 'integer',
    ];
}
