<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'business_category',
        'sub_business_category',
        'join_date',
        'telegram_chat_id',
        'status',
        'user_marketing_id',
        'owner',
        'pos_status',
    ];

    protected $casts = [
        'join_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function userMarketing()
    {
        return $this->belongsTo(User::class, 'user_marketing_id');
    }

    public function dtQueues()
    {
        return $this->hasMany(DtQueue::class, 'Chat_Id', 'telegram_chat_id');
    }

    public function dtInvoices()
    {
        return $this->hasMany(DtInvoice::class, 'Chat_Id', 'telegram_chat_id');
    }
}
