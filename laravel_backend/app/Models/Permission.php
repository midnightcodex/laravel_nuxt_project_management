<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['role_id', 'entity_type', 'entity_id', 'read_permission', 'write_permission'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
