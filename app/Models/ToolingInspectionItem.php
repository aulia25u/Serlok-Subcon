<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolingInspectionItem extends Model
{
    use HasFactory;

    protected $table = 'tooling_inspection_items';

    protected $fillable = [
        'tooling_inspection_id',
        'row_number',
        'inspection_item',
        'inspection_method',
        'standard',
        'tooling_1',
        'tooling_2',
        'tooling_3',
        'tooling_4',
        'tooling_5',
        'tooling_6',
        'tooling_7',
        'tooling_8',
        'tooling_9',
        'tooling_10',
        'x_bar',
        'r_value',
    ];

    /**
     * Relationship to Tooling Inspection
     */
    public function toolingInspection()
    {
        return $this->belongsTo(ToolingInspection::class, 'tooling_inspection_id');
    }
}
