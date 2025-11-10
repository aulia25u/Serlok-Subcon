<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomCustomer extends Model
{
    use HasFactory;

    protected $table = 'mom_customers';

    protected $fillable = [
        'customer_id',
        'meeting_date',
        'attendees',
        'agenda',
        'minutes',
        'action_items',
        'next_meeting_date',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'next_meeting_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
