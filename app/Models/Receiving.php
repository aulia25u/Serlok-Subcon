<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_item_id',
        'doc_number_internal',
        'qrcode_customer',
        'doc_number_customer',
        'product_status',
        'delivery_date_customer',
        'incoming_date',
        'receive_by',
        'qty_pack',
        'qty_per_pack',
        'delivery_by',
        'ng_customer',
        'ng_operator',
    ];

    protected $casts = [
        'delivery_date_customer' => 'date',
        'incoming_date' => 'datetime',
    ];

    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receive_by');
    }

    public function ngOperator()
    {
        return $this->belongsTo(User::class, 'ng_operator');
    }
}
