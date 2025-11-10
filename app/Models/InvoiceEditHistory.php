<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceEditHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_edit_history';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'process_id',
        'telegram_chat_id',
        'field_name',
        'old_value',
        'new_value',
        'old_total',
        'new_total',
        'edited_by',
        'editor_name',
        'edited_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'old_total' => 'decimal:2',
        'new_total' => 'decimal:2',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the user who edited the invoice.
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get the invoice associated with this history.
     */
    public function invoice()
    {
        return $this->belongsTo(DtInvoice::class, 'invoice_id');
    }
}
