<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Itempart;
use App\Models\UploadHasilMeeting;
use App\Models\UploadDrawingPart;

class LOIInternal extends Model
{
    protected $table = 'loi_internals';

    protected $fillable = [
        'rfqmaster_id',
        'document_no',
        'document_date',
        'customer_name',
    ];

    protected $casts = [
        'document_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to get parts data directly
    public function part()
    {
        return $this->belongsTo(Itempart::class, 'rfqmaster_id', 'id');
    }

    // Relationship to get upload hasil meetings by part_id
    public function uploadHasilMeetings()
    {
        return $this->hasManyThrough(
            UploadHasilMeeting::class,
            Itempart::class,
            'id', // Foreign key on parts table
            'part_id', // Foreign key on upload_hasil_meetings table
            'rfqmaster_id', // Local key on loi_internals table
            'id' // Local key on parts table
        );
    }

    // Relationship to get upload drawing parts by part_id
    public function uploadDrawingParts()
    {
        return $this->hasManyThrough(
            UploadDrawingPart::class,
            Itempart::class,
            'id', // Foreign key on parts table
            'part_id', // Foreign key on upload_drawing_parts table
            'rfqmaster_id', // Local key on loi_internals table
            'id' // Local key on parts table
        );
    }
}
