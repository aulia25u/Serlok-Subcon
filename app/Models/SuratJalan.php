<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratJalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'document_number',
        'surat_jalan_date',
        'status',
        'employee_job_id',
        'customer_id',
        'known_by',
    ];

    protected $casts = [
        'surat_jalan_date' => 'datetime',
    ];

    public function employeeJob()
    {
        return $this->belongsTo(EmployeeJob::class);
    }

    public function customer()
    {
        return $this->belongsTo(MasterCustomer::class, 'customer_id');
    }
}
