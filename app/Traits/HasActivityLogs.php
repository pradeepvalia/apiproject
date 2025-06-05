<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait HasActivityLogs
{
    protected static function bootHasActivityLogs()
    {
        static::created(function ($model) {
            ActivityLogService::logCreate(
                class_basename($model),
                "Created new " . class_basename($model),
                $model->toArray()
            );
        });

        static::updated(function ($model) {
            ActivityLogService::logUpdate(
                class_basename($model),
                "Updated " . class_basename($model),
                $model->getOriginal(),
                $model->getChanges()
            );
        });

        static::deleted(function ($model) {
            ActivityLogService::logDelete(
                class_basename($model),
                "Deleted " . class_basename($model),
                $model->toArray()
            );
        });
    }
}
