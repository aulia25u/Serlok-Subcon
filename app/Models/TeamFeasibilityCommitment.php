<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamFeasibilityCommitment extends Model
{
    use HasFactory;

    protected $table = 'team_feasibility_commitments';

    protected $fillable = [
        'document_no',
        'part_name',
        'part_no',
        'model',
        'customer_name',
        'conclusion_status', // feasible, feasible_with_changes, not_feasible
        'conclusion_notes',
        'submitted_at',
        'approved_at',
        'approved_by',
        'created_by',
        'updated_by',
        // Sign-off fields
        'general_mgr_id',
        'general_mgr_signed_at',
        'factory_mgr_id',
        'factory_mgr_signed_at',
        'qa_mgr_id',
        'qa_mgr_signed_at',
        'qc_id',
        'qc_signed_at',
        'engineering_id',
        'engineering_signed_at',
        'production_id',
        'production_signed_at',
        'maintenance_id',
        'maintenance_signed_at',
        'ppic_id',
        'ppic_signed_at',
        'purchasing_id',
        'purchasing_signed_at',
        'sales_id',
        'sales_signed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'general_mgr_signed_at' => 'datetime',
        'factory_mgr_signed_at' => 'datetime',
        'qa_mgr_signed_at' => 'datetime',
        'qc_signed_at' => 'datetime',
        'engineering_signed_at' => 'datetime',
        'production_signed_at' => 'datetime',
        'maintenance_signed_at' => 'datetime',
        'ppic_signed_at' => 'datetime',
        'purchasing_signed_at' => 'datetime',
        'sales_signed_at' => 'datetime',
    ];

    /**
     * Relationship to checklist items
     */
    public function checklistItems()
    {
        return $this->hasMany(TeamFeasibilityChecklistItem::class, 'feasibility_commitment_id');
    }

    /**
     * Relationship to revisions
     */
    public function revisions()
    {
        return $this->hasMany(TeamFeasibilityRevision::class, 'feasibility_commitment_id')->orderBy('revision_number', 'asc');
    }

    /**
     * Relationship to creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship to approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get latest revision
     */
    public function latestRevision()
    {
        return $this->hasOne(TeamFeasibilityRevision::class, 'feasibility_commitment_id')
            ->latestOfMany('revision_number');
    }

    // Sign-off relationships
    public function generalMgr()
    {
        return $this->belongsTo(User::class, 'general_mgr_id');
    }

    public function factoryMgr()
    {
        return $this->belongsTo(User::class, 'factory_mgr_id');
    }

    public function qaMgr()
    {
        return $this->belongsTo(User::class, 'qa_mgr_id');
    }

    public function qc()
    {
        return $this->belongsTo(User::class, 'qc_id');
    }

    public function engineering()
    {
        return $this->belongsTo(User::class, 'engineering_id');
    }

    public function production()
    {
        return $this->belongsTo(User::class, 'production_id');
    }

    public function maintenance()
    {
        return $this->belongsTo(User::class, 'maintenance_id');
    }

    public function ppic()
    {
        return $this->belongsTo(User::class, 'ppic_id');
    }

    public function purchasing()
    {
        return $this->belongsTo(User::class, 'purchasing_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }
}
