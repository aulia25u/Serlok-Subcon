<?php

namespace App\Models;

use App\Models\ski\RfqmasterSKI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolingInspection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tooling_inspections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'customer',
        'date',
        'part_no',
        'quantity',
        'image',
        'result',
        'tooling_type',
        'note',
        'inspected_by',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'customer_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to Inspector (User)
     */
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    /**
     * Relationship to Approver (User)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship to Inspection Items (One to Many)
     */
    public function items()
    {
        return $this->hasMany(ToolingInspectionItem::class, 'tooling_inspection_id')->orderBy('row_number');
    }
}
