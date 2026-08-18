<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            static::writeAudit($model, 'Creado', [], static::cleanValues($model->getAttributes()));
        });

        static::updated(function (Model $model): void {
            $changes = collect($model->getChanges())->except(['updated_at'])->all();

            if ($changes === []) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($changes) as $field) {
                $oldValues[$field] = $model->getOriginal($field);
            }

            static::writeAudit($model, 'Actualizado', static::cleanValues($oldValues), static::cleanValues($changes));
        });

        static::deleted(function (Model $model): void {
            static::writeAudit($model, 'Eliminado', static::cleanValues($model->getOriginal()), []);
        });
    }

    protected static function writeAudit(Model $model, string $action, array $oldValues, array $newValues): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'occurred_at' => now(),
            'module' => method_exists($model, 'auditModule')
                ? $model->auditModule()
                : Str::headline(class_basename($model)),
            'record_type' => $model::class,
            'record_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : request()->userAgent(),
        ]);
    }

    protected static function cleanValues(array $values): array
    {
        return collect($values)
            ->except(['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'])
            ->all();
    }
}
