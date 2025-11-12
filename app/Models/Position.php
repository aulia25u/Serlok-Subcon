<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'position_name',
        'section_id',
        'customer_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
