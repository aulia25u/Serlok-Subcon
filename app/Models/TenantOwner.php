<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;

class TenantOwner extends Model
{
    use HasFactory;

    protected $table = 'tenant_owners';

    protected $fillable = [
        'user_id',
        'customer_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getNameAttribute()
    {
        return $this->customer ? $this->customer->customer_name : 'N/A';
    }
}
