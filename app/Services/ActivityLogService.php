<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public static function log($module, $action, $description, $oldValues = null, $newValues = null)
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logCreate($module, $description, $newValues = null)
    {
        return self::log($module, 'created', $description, null, $newValues);
    }

    public static function logUpdate($module, $description, $oldValues, $newValues)
    {
        return self::log($module, 'updated', $description, $oldValues, $newValues);
    }

    public static function logDelete($module, $description, $oldValues = null)
    {
        return self::log($module, 'deleted', $description, $oldValues);
    }
}
