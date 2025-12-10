<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'outgoing_id',
        'user_id',
        'created_datetime',
        'start_datetime',
        'finished_datetime',
        'qty_ok',
        'qty_ng',
        'qty_ng_customer',
        'inspector_id',
        'surat_jalan_status',
    ];

    protected $casts = [
        'created_datetime' => 'datetime',
        'start_datetime' => 'datetime',
        'finished_datetime' => 'datetime',
    ];

    public function outgoing()
    {
        return $this->belongsTo(Outgoing::class);
    }

    public function user() // Employee
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function suratJalan()
    {
        return $this->hasOne(SuratJalan::class);
    }
}
