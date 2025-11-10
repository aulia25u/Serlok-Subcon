<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInformation extends Model
{
    use HasFactory;

    protected $table = 'sales_informations';

    protected $fillable = [
        'date',
        'customer_id',
        'part_no',
        'part_name',
        'date_masspro',
        'qty_month',
        'depreciation_periode',
        'tools_depreciation',
        'part_type', // Changed from critical_safety and regular_part
        'similar_part',
        'model', // Changed from array to string
        'waya_ply_value',
        'wrapping_ply_value',
        'model_other_value',
        'note',
        'decision',
        'approved_by',
        'checked_by_1',
        'checked_by_2',
        'prepared_by',
    ];

    protected $casts = [
        'date' => 'date',
        'date_masspro' => 'date',
        'similar_part' => 'boolean',
    ];

    // Relationships
    public function productionProcess()
    {
        return $this->hasOne(ProductionProcessInformation::class);
    }

    public function materialCalculations()
    {
        return $this->hasMany(MaterialCalculation::class);
    }

    public function additionalParts()
    {
        return $this->hasMany(AdditionalPart::class);
    }

    public function manufacturingProcesses()
    {
        return $this->hasMany(ManufacturingProcess::class);
    }

    public function importantPoints()
    {
        return $this->hasMany(ImportantPoint::class);
    }

    public function toolings()
    {
        return $this->hasMany(Tooling::class);
    }

    // Approval Relationships
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function checkedBy1()
    {
        return $this->belongsTo(User::class, 'checked_by_1');
    }

    public function checkedBy2()
    {
        return $this->belongsTo(User::class, 'checked_by_2');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
