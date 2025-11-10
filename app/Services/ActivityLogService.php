<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log($action, $tableName, $recordId, $oldValues = null, $newValues = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
        ]);
    }

    public static function logCreate($tableName, $recordId, $newValues = null)
    {
        self::log('create', $tableName, $recordId, null, $newValues);
    }

    public static function logUpdate($tableName, $recordId, $oldValues = null, $newValues = null)
    {
        self::log('update', $tableName, $recordId, $oldValues, $newValues);
    }

    public static function logDelete($tableName, $recordId, $oldValues = null)
    {
        self::log('delete', $tableName, $recordId, $oldValues, null);
    }
}
