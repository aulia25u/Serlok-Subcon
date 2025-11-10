<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtLogFailed extends Model
{
    protected $connection = 'mysql_invoice';
    protected $table = 'dt_log_failed';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Chat_Id', 'telegram_chat_id');
    }

    public function image()
    {
        return $this->hasOne(DtImage::class, 'ProcessID', 'ProcessID');
    }
}