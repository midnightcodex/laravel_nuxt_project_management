<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ItemCycle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['item_id', 'status', 'updated_by'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
