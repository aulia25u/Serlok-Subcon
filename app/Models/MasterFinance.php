<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
    ];

    public function tenantOwner()
    {
        return $this->belongsTo(TenantOwner::class, 'tenant_id');
    }
}
