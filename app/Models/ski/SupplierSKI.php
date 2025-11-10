<?php

namespace App\Models\ski;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierSKI extends Model
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
    protected $table = 'suppliers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'name',
        'telp',
        'fax',
        'alamat',
        'attn',
        'images',
        'active',
        'payment',
        'rev_no',
        'office',
        'office_city_id',
        'office_telp',
        'office_fax',
        'office_established',
        'plant1',
        'plant1_city_id',
        'plant1_telp',
        'plant1_fax',
        'plant1_established',
        'plant2',
        'plant2_city_id',
        'plant2_telp',
        'plant2_fax',
        'plant2_established',
        'plant3',
        'plant3_city_id',
        'plant3_telp',
        'plant3_fax',
        'plant3_established',
        'type_of_company',
        'main_product',
        'number_of_employee',
        'working_shift',
        'shift',
        'director_name',
        'director_position',
        'director_mobile',
        'director_email',
        'purchasing_name',
        'purchasing_position',
        'purchasing_mobile',
        'purchasing_email',
        'production_name',
        'production_position',
        'production_mobile',
        'production_email',
        'finance_name',
        'finance_position',
        'finance_mobile',
        'finance_email',
        'quality_name',
        'quality_position',
        'quality_mobile',
        'quality_email',
        'total_asset',
        'annual_sales',
        'npwp',
        'bank_id',
        'branches',
        'currency_id',
        'account_no',
        'tax',
        'customer_industry',
        'automotive_type',
        'sales_type',
        'user_id',
        'checkedby',
        'approvedby',
        'madeby_cust',
        'checkedby_cust',
        'approvedby_cust',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'integer',
        'office_city_id' => 'integer',
        'office_established' => 'date',
        'plant1_city_id' => 'integer',
        'plant1_established' => 'date',
        'plant2_city_id' => 'integer',
        'plant2_established' => 'date',
        'plant3_city_id' => 'integer',
        'plant3_established' => 'date',
        'number_of_employee' => 'integer',
        'total_asset' => 'decimal:2',
        'annual_sales' => 'decimal:2',
        'bank_id' => 'integer',
        'currency_id' => 'integer',
        'user_id' => 'integer',
        'checkedby' => 'integer',
        'approvedby' => 'integer',
    ];
}
