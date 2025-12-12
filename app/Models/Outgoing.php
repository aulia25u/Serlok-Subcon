<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outgoing extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_item_id',
        'user_id',
        'quantity',
        'outgoing_date',
        'notes',
        'created_by',
        'status',
    ];

    protected $casts = [
        'outgoing_date' => 'datetime',
    ];

    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employeeJob()
    {
        return $this->hasOne(EmployeeJob::class);
    }
}
