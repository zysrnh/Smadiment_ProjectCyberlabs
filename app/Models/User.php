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
        'avatar',
        'trial_ends_at',
        'subscription_notice_at',
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
            'trial_ends_at' => 'datetime',
            'subscription_notice_at' => 'datetime',
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

    /**
     * 🔥 NEW: Get remaining days of trial
     */
    public function trialRemainingDays(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }

        if ($this->trial_ends_at->isPast()) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->trial_ends_at->startOfDay());
    }

    /**
     * 🔥 NEW: Check if trial is active
     */
    public function isTrialActive(): bool
    {
        if (!$this->trial_ends_at) {
            return true; // If no trial set, assume permanent access for now or handle differently
        }

        return now()->lessThanOrEqualTo($this->trial_ends_at->endOfDay());
    }
}