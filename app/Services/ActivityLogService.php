<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log($action, $tableName, $recordId, $oldValues = null, $newValues = null, $targetUserId = null)
    {
        $user = Auth::user();
        $tenantId = $user?->userDetail?->customer_id;

        ActivityLog::create([
            'user_id' => $user?->id,
            'target_user_id' => $targetUserId,
            'tenant_id' => $tenantId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function logCreate($tableName, $recordId, $newValues = null, $targetUserId = null)
    {
        self::log('create', $tableName, $recordId, null, $newValues, $targetUserId);
    }

    public static function logUpdate($tableName, $recordId, $oldValues = null, $newValues = null, $targetUserId = null)
    {
        self::log('update', $tableName, $recordId, $oldValues, $newValues, $targetUserId);
    }

    public static function logDelete($tableName, $recordId, $oldValues = null, $targetUserId = null)
    {
        self::log('delete', $tableName, $recordId, $oldValues, null, $targetUserId);
    }
}
