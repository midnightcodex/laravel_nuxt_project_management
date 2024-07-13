<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TaskDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['item_id', 'task_due_date', 'task_priority', 'task_type'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

