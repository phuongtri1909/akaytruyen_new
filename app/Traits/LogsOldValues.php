<?php

namespace App\Traits;

trait LogsOldValues
{
    protected static $oldValues = [];

    protected static function bootLogsOldValues()
    {
        static::updating(function ($model) {
            static::$oldValues[$model->getKey()] = $model->getOriginal();
        });

        static::deleting(function ($model) {
            static::$oldValues[$model->getKey()] = $model->getAttributes();
        });
    }

    public static function getOldValues($id)
    {
        return static::$oldValues[$id] ?? null;
    }

    public static function clearOldValues($id = null)
    {
        if ($id) {
            unset(static::$oldValues[$id]);
        } else {
            static::$oldValues = [];
        }
    }
}
