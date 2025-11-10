<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterInspection extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_inspections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inspection_item',
        'inspection_method',
        'standard',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
