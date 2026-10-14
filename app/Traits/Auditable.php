<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'Create');
        });

        static::updated(function ($model) {
            self::logAction($model, 'Update');
        });

        static::deleted(function ($model) {
            self::logAction($model, 'Delete');
        });
    }

    protected static function logAction($model, $action)
    {
        $moduleName = class_basename($model);
        
        $oldValues = $action !== 'Create' ? $model->getOriginal() : null;
        $newValues = $action !== 'Delete' ? $model->getAttributes() : null;

        AuditLog::record(
            $moduleName, 
            $action, 
            "{$action}d {$moduleName} (ID: {$model->id})", 
            $oldValues, 
            $newValues
        );
    }
}