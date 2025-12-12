<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'target_user_id',
        'tenant_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'tenant_id');
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'create' => '<span class="badge badge-success">Create</span>',
            'update' => '<span class="badge badge-warning">Update</span>',
            'delete' => '<span class="badge badge-danger">Delete</span>',
            'login' => '<span class="badge badge-info">Login</span>',
            'login_failed' => '<span class="badge badge-danger">Login Failed</span>',
            'logout' => '<span class="badge badge-secondary">Logout</span>',
        ];

        return $badges[$this->action] ?? '<span class="badge badge-secondary">' . $this->action . '</span>';
    }

    public function getBrowserAttribute()
    {
        if (!$this->user_agent)
            return '-';

        $agent = $this->user_agent;
        $browser = 'Unknown';
        $version = '';

        if (preg_match('/MSIE/i', $agent) && !preg_match('/Opera/i', $agent)) {
            $browser = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $agent)) {
            $browser = 'Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $agent)) {
            $browser = 'Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $agent)) {
            $browser = 'Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $agent)) {
            $browser = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $agent)) {
            $browser = 'Netscape';
            $ub = "Netscape";
        } else {
            return 'Unknown';
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $agent, $matches)) {
            // we have no matching number just continue
        }

        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($agent, "Version") < strripos($agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1] ?? 'Unknown';
            }
        } else {
            $version = $matches['version'][0];
        }

        return $browser . ' ' . $version;
    }

    public function getPlatformAttribute()
    {
        if (!$this->user_agent)
            return '-';

        $agent = $this->user_agent;
        $platform = 'Unknown OS';

        $os_array = [
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $agent)) {
                $platform = $value;
                break;
            }
        }

        return $platform;
    }

    public function getTableNameFormattedAttribute()
    {
        return match ($this->table_name) {
            'user_details' => 'User Data',
            'depts' => 'Department',
            'sections' => 'Section',
            'positions' => 'Position',
            'roles' => 'Role',
            default => ucfirst(str_replace('_', ' ', $this->table_name)),
        };
    }
}
