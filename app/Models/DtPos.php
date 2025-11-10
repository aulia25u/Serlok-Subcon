<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtPos extends Model
{
    use HasFactory;

    protected $connection = 'mysql_invoice';
    protected $table = 'dt_pos';

    protected $fillable = [
        'outlet',
        'telegram_chat_id',
        'receipt_number',
        'date',
        'time',
        'category',
        'brand',
        'items',
        'variant',
        'sku',
        'quantity',
        'modifier_applied',
        'discount_applied',
        'gross_sales',
        'discounts',
        'refunds',
        'net_sales',
        'gratuity',
        'tax',
        'sales_type',
        'collected_by',
        'served_by',
        'customer',
        'payment_method',
        'event_type',
        'reason_of_refund',
    ];

    protected $casts = [
        'gross_sales' => 'decimal:2',
        'discounts' => 'decimal:2',
        'refunds' => 'decimal:2',
        'net_sales' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'tax' => 'decimal:2',
        'quantity' => 'integer',
    ];
}
