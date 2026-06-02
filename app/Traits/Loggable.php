<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\Audit;

trait Loggable
{
    /**
     * Boot the trait to listen for model events.
     */
    public static function bootLoggable()
    {
        // Log on create
        static::created(function ($model) {
            Activity::log("Created {$model->getTable()}", $model, ['action' => 'created']);
            Audit::record('created', $model, [], $model->getAttributes());
        });

        // Log on update
        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            Activity::log("Updated {$model->getTable()}", $model, ['action' => 'updated', 'changes' => $changes]);
            Audit::record('updated', $model, $original, $changes);
        });

        // Log on delete
        static::deleted(function ($model) {
            Activity::log("Deleted {$model->getTable()}", $model, ['action' => 'deleted']);
            Audit::record('deleted', $model, $model->getAttributes(), []);
        });
    }
}
