<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadLOIExternal extends Model
{
    protected $table = 'upload_loi_internals';

    protected $fillable = [
        'part_id',
        'type',
        'title',
        'image'
    ];
}
