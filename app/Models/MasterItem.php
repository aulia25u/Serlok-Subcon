<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'master_customer_id',
        'item_name',
        'item_code',
        'product_status',
        'part_number',
        'model',
        'unit',
        'description',
    ];

    public function tenantOwner()
    {
        return $this->belongsTo(TenantOwner::class, 'tenant_id');
    }

    public function masterCustomer()
    {
        return $this->belongsTo(MasterCustomer::class, 'master_customer_id');
    }
}
