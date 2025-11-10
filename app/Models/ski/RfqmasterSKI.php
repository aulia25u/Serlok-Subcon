<?php

namespace App\Models\ski;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqmasterSKI extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'shimada_ski';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rfqmasters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partno',
        'partname',
        'date_created',
        'customer_id',
        'status',
        'image',
        'dom',
        'qty',
        'material',
        'dp',
        'td',
        'availability',
        'modelpart',
        'typepart',
        'std_d',
        'std_e',
        'std_f',
        'std_f2',
        'std_g',
        'std_h',
        'std_i',
        'std_j',
        'std_k',
        'frek_a',
        'frek_b',
        'similairpart',
        'pdf',
        'tooling_id',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_created' => 'date',
        'dom' => 'date',
        'customer_id' => 'integer',
        'tooling_id' => 'integer',
    ];

    /**
     * Get the customer associated with the RFQ.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerSKI::class, 'customer_id');
    }
}
