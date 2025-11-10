<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalPart extends Model
{
    use HasFactory;

    protected $table = 'additional_parts';

    protected $fillable = [
        'sales_information_id',
        'material_id',
        'part_no',
        'specification',
        'qty_unit',
        'supplier',
    ];

    protected $casts = [
        'qty_unit' => 'integer',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }

    public function material()
    {
        return $this->belongsTo(\App\Models\ski\MaterialSKI::class, 'material_id');
    }
}
