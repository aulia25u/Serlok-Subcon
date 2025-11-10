<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamFeasibilityChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'team_feasibility_checklist_items';

    protected $fillable = [
        'feasibility_commitment_id',
        'item_code', // unique code for each checklist item, example : 1,1.1, 1.2, 2, etc.
        'checkpoint_description',
        'check_result', // ok, tidak_ok, null
        'pic', // Person in Charge (ENG, SLS, PPIC, etc.)
        'notes',
        'is_checkbox', // if this item has checkbox options
        'checkbox_value', // for  "Ya, yaitu:"
        'order_sequence',
    ];

    protected $casts = [
        'is_checkbox' => 'boolean',
    ];

    /**
     * Relationship to main commitment
     */
    public function feasibilityCommitment()
    {
        return $this->belongsTo(TeamFeasibilityCommitment::class, 'feasibility_commitment_id');
    }
}
