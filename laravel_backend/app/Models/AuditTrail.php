<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuditTrail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

