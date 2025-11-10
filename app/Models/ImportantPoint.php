<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportantPoint extends Model
{
    use HasFactory;

    protected $table = 'important_points';

    protected $fillable = [
        'sales_information_id',
        'item',
        'note',
    ];

    // Relationship
    public function salesInformation()
    {
        return $this->belongsTo(SalesInformation::class);
    }
}
