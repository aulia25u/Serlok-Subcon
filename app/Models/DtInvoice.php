<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtInvoice extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_invoice';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dt_invoice';

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
        'ProcessID',
        'Chat_Id',
        'Tanggal_Input',
        'Waktu_Input',
        'Nomor_Invoice',
        'Toko',
        'Items',
        'Jumlah',
        'Satuan',
        'Harga_Satuan',
        'Total_Per_Item',
        'Tanggal_Struk',
        'Input_By',
        'File_Name',
        'File_Link',
        'Sync_status',
        'Sync_time',
        'checked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'Tanggal_Input' => 'date',
        'Waktu_Input' => 'datetime',
        'Jumlah' => 'decimal:2',
        'Harga_Satuan' => 'decimal:2',
        'Total_Per_Item' => 'decimal:2',
        'Tanggal_Struk' => 'date',
        'Sync_time' => 'datetime',
        'checked' => 'boolean',
    ];

    /**
     * Get the customer associated with the invoice.
     * This relationship crosses databases.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Chat_Id', 'telegram_chat_id');
    }
}
