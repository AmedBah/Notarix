<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Traceable
{
    protected static function bootTraceable()
    {
        static::created(function ($model) {
            self::logActivity('creation', $model);
        });

        static::updated(function ($model) {
            self::logActivity('modification', $model, $model->getOriginal());
        });

        static::deleted(function ($model) {
            self::logActivity('suppression', $model);
        });
    }

    protected static function logActivity($action, $model, $oldValues = null)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'model_name' => $model->nom ?? $model->name ?? 'N/A',
                'old_values' => $oldValues,
                'new_values' => $model->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    // Méthode pour tracer les consultations
    public function logConsultation()
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'consultation',
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'model_name' => $this->nom ?? $this->name ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    // Méthode pour tracer les téléchargements
    public function logDownload()
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'telecharger',
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'model_name' => $this->nom ?? $this->name ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }
}
