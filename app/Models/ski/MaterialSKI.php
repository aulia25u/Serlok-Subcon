<?php

namespace App\Models\ski;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialSKI extends Model
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
    protected $table = 'materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coding',
        'kode_nomer',
        'kode_material',
        'name',
        'image',
        'category_id',
        'supplier_id',
        'unit',
        'harga',
        'currency_code',
        'stokin',
        'stockpo',
        'stokout',
        'totalstok',
        'status',
        'nilai_bawah_hs',
        'nilai_atas_hs',
        'nilai_bawah_tb',
        'nilai_atas_tb',
        'nilai_bawah_eb',
        'nilai_atas_eb',
        'nilai_bawah_sg',
        'nilai_atas_sg',
        'bmmin',
        'bmmax',
        'tb',
        'eb',
        'hs',
        'sg',
        'bm',
        'standar_mix',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id' => 'integer',
        'supplier_id' => 'integer',
        'harga' => 'decimal:2',
        'stokin' => 'decimal:0',
        'totalstok' => 'float',
        'nilai_bawah_hs' => 'integer',
        'nilai_atas_hs' => 'integer',
        'nilai_bawah_tb' => 'integer',
        'nilai_atas_tb' => 'integer',
        'nilai_bawah_eb' => 'integer',
        'nilai_atas_eb' => 'integer',
        'nilai_bawah_sg' => 'float',
        'nilai_atas_sg' => 'float',
        'bmmin' => 'integer',
        'bmmax' => 'integer',
        'standar_mix' => 'float',
    ];
}
