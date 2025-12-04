<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_item_id',
        'quantity',
    ];

    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class);
    }
}
