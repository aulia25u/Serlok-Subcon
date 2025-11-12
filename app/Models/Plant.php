<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Plant extends Model
{
    use HasFactory;

    protected $table = 'plants';

    protected $fillable = [
        'plant_name',
        'plant_code',
        'location',
        'description',
        'customer_id',
    ];

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
