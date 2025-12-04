<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'position_id',
        'role_id',
        'customer_id',
        'plant_id',
        'employee_id',
        'nip',
        'employee_name',
        'employee_status',
        'blacklist_note',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'gender',
        'address',
        'phone',
        'join_date',
        'status_active',
        'employee_photo',
    ];

    protected $casts = [
        'join_date' => 'datetime',
        'status_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
