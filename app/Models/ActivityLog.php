<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'create' => '<span class="badge badge-success">Created</span>',
            'update' => '<span class="badge badge-warning">Updated</span>',
            'delete' => '<span class="badge badge-danger">Deleted</span>',
            default => '<span class="badge badge-secondary">' . ucfirst($this->action) . '</span>',
        };
    }

    public function getTableNameFormattedAttribute()
    {
        return match($this->table_name) {
            'user_details' => 'User Data',
            'depts' => 'Department',
            'sections' => 'Section',
            'positions' => 'Position',
            'roles' => 'Role',
            default => ucfirst(str_replace('_', ' ', $this->table_name)),
        };
    }
}
