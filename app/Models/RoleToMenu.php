<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleToMenu extends Model
{
    use HasFactory;

    protected $table = 'role_to_menu';

    protected $fillable = [
        'role_id',
        'menu_id',
        'is_create',
        'is_read',
        'is_update',
        'is_delete',
    ];

    protected $casts = [
        'is_create' => 'boolean',
        'is_read' => 'boolean',
        'is_update' => 'boolean',
        'is_delete' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}

