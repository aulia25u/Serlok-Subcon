<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtImage extends Model
{
    protected $connection = 'mysql_invoice';
    protected $table = 'dt_image';
}