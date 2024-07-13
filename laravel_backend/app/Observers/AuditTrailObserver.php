<?php

namespace App\Observers;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditTrail;

class AuditTrailObserver
{
    public function created(Model $model)
    {
        $this->logAuditTrail($model, 'created');
    }

    public function updated(Model $model)
    {
        $this->logAuditTrail($model, 'updated');
    }

    public function deleted(Model $model)
    {
        $this->logAuditTrail($model, 'deleted');
    }

    protected function logAuditTrail(Model $model, string $action)
    {
        AuditTrail::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
        ]);
    }
}
