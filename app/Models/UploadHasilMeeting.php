<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadHasilMeeting extends Model
{
    protected $table = 'upload_hasil_meetings';

    protected $fillable = [
        'part_id',
        'type',
        'title',
        'image'
    ];
}
