<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialCalculation extends Model
{
    use HasFactory;

    protected $table = 'material_calculations';

    protected $fillable = [
        'sales_information_id',
        'material_id',
        'specification',
        'new_material',
        'code',
        'thick',
        'diameter_in',
        'diameter_out',
        'length',
        'volume',
        'weight_estimate',
        'weight_actual',
    ];

    protected $casts = [
        'thick' => 'decimal:2',
        'diameter_in' => 'decimal:2',
        'diameter_out' => 'decimal:2',
        'length' => 'decimal:2',
        'volume' => 'decimal:2',
        'weight_estimate' => 'decimal:2',
        'weight_actual' => 'decimal:2',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }
}
