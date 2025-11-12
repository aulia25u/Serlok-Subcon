<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'customer_name',
        'customer_code',
        'address',
        'npwp',
    ];
    public function tenantOwner()
    {
        return $this->belongsTo(TenantOwner::class, 'tenant_id');
    }
}
