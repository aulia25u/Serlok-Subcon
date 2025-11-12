<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'item_name',
        'item_code',
        'description',
    ];

    public function tenantOwner()
    {
        return $this->belongsTo(TenantOwner::class, 'tenant_id');
    }
}
