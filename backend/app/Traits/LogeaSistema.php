<?php

namespace App\Traits;

use App\Models\LogSistema;

trait LogeaSistema
{
    public static function bootLogeaSistema()
    {
        static::updated(function ($model) {
            $cambios = $model->getChanges();
            unset($cambios['updated_at']);
            if (empty($cambios)) return;

            LogSistema::registrar(
                'ACTUALIZAR',
                class_basename($model) . " ID {$model->id}",
                $model->getOriginal(),
                $cambios,
                $model->getTable()
            );
        });

        static::created(function ($model) {
            LogSistema::registrar(
                'CREAR',
                class_basename($model) . " ID {$model->id}",
                null,
                $model->toArray(),
                $model->getTable()
            );
        });

        static::deleted(function ($model) {
            LogSistema::registrar(
                'ELIMINAR',
                class_basename($model) . " ID {$model->id}",
                $model->toArray(),
                null,
                $model->getTable()
            );
        });
    }
}
