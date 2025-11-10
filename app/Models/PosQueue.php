<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosQueue extends Model
{
    use HasFactory;

    protected $table = 'pos_queue';

    protected $fillable = [
        'customer_id',
        'start_date',
        'end_date',
        'telegram_chat_id',
        'status',
        'is_scheduled',
        'schedule_time',
        'last_run',
    ];

    protected $casts = [
        'last_run' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
