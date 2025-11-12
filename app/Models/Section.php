<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

    protected $fillable = [
        'section_name',
        'dept_id',
        'customer_id',
    ];

    public function dept()
    {
        return $this->belongsTo(Dept::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
