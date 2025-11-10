<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturingProcess extends Model
{
    use HasFactory;

    protected $table = 'manufacturing_processes';

    protected $fillable = [
        'sales_information_id',
        'process_name',
        'enabled',
        'machine_id',
        'cycle_time_estimate',
        'cycle_time_actual',
        'capacity_estimate',
        'capacity_actual',
        'remarks',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'cycle_time_estimate' => 'decimal:2',
        'cycle_time_actual' => 'decimal:2',
        'capacity_estimate' => 'decimal:2',
        'capacity_actual' => 'decimal:2',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }
}
