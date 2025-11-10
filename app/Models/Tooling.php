<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tooling extends Model
{
    use HasFactory;

    protected $table = 'toolings';

    protected $fillable = [
        'sales_information_id',
        'tooling',
        'cavity',
        'quantity',
    ];

    protected $casts = [
        'cavity' => 'integer',
        'quantity' => 'integer',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }
}
