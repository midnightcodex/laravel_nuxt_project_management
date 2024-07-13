<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeadDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['item_id', 'lead_source', 'lead_value', 'potential_close_date'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id'); 
    }
}
