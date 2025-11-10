<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamFeasibilityRevision extends Model
{
    use HasFactory;

    protected $table = 'team_feasibility_revisions';

    protected $fillable = [
        'feasibility_commitment_id',
        'revision_number',
        'revision_date',
        'revision_contains',
        'revised_by',
        'notes',
    ];

    protected $casts = [
        'revision_date' => 'date',
    ];

    /**
     * Relationship to main commitment
     */
    public function feasibilityCommitment()
    {
        return $this->belongsTo(TeamFeasibilityCommitment::class, 'feasibility_commitment_id');
    }

    /**
     * Relationship to reviser
     */
    public function reviser()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }
}
