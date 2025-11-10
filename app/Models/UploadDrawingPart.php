<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadDrawingPart extends Model
{
    protected $table = 'upload_drawing_parts';

    protected $fillable = [
        'part_id',
        'type',
        'title',
        'image'
    ];
}
