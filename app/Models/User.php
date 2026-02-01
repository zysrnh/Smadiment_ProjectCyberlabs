<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 🔥 NEW: Relationship ke project assignments
     */
    public function projectAssignments()
    {
        return $this->hasMany(UserProject::class);
    }

    /**
     * 🔥 NEW: Get array of assigned project IDs
     */
    public function assignedProjectIds(): array
    {
        return $this->projectAssignments()->pluck('project_id')->toArray();
    }

    /**
     * 🔥 NEW: Check if user has access to a project
     */
    public function hasAccessToProject(int $projectId): bool
    {
        return $this->projectAssignments()
            ->where('project_id', $projectId)
            ->exists();
    }
}