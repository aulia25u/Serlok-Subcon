<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCapture extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_item_id',
        'quantity',
        'captured_at',
        'physical_quantity',
        'is_adjusted',
        'adjusted_at',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'adjusted_at' => 'datetime',
        'physical_quantity' => 'double',
        'is_adjusted' => 'boolean',
    ];

    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class, 'master_item_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
