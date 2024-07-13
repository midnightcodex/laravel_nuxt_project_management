<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Project;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects() : HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function createdItems()
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    public function assignedItems()
    {
        return $this->hasMany(Item::class, 'assigned_to');
    }

    public function itemCycles()
    {
        return $this->hasMany(ItemCycle::class, 'updated_by');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'employee_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'employee_id');
    }

    public function auditTrail()
    {
        return $this->hasMany(AuditTrail::class, 'user_id');
    }
}
