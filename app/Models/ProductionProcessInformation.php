<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionProcessInformation extends Model
{
    use HasFactory;

    protected $table = 'production_process_informations';

    protected $fillable = [
        'sales_information_id',
        'process_location',
        'supplier_name',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }
}
