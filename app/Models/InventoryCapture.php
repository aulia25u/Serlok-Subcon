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
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class);
    }
}
