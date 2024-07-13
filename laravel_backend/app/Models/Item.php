<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['title', 'description', 'status', 'type', 'created_by', 'assigned_to'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leadDetail()
    {
        return $this->hasOne(LeadDetail::class, 'item_id', 'id');
    }

    public function taskDetail()
    {
        return $this->hasOne(TaskDetail::class, 'item_id', 'id');
    }

    public function itemCycle()
    {
        return $this->hasOne(LeadDetail::class, 'item_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}

