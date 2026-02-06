<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Allowed values for the user's professional role (used in validation).
     *
     * @var list<string>
     */
    public const ALLOWED_ROLES = [
        'owner',
        'admin',
        'backend',
        'frontend',
        'qa',
        'designer',
        'project_manager',
    ];

    /**
     * Role badge CSS classes for Admin UI (role key => Tailwind classes).
     *
     * @var array<string, string>
     */
    public const ROLE_BADGE_CLASSES = [
        'qa' => 'bg-orange-100 text-orange-800',
        'designer' => 'bg-purple-100 text-purple-800',
        'project_manager' => 'bg-cyan-100 text-cyan-800',
        'owner' => 'bg-gray-100 text-gray-800',
        'admin' => 'bg-indigo-100 text-indigo-800',
        'backend' => 'bg-indigo-100 text-indigo-800',
        'frontend' => 'bg-emerald-100 text-emerald-800',
    ];

    /**
     * Display name for the user's role (e.g. "Owner", "Admin", "Project manager").
     * Use in Blade as {{ Auth::user()->role_name }}.
     */
    public function getRoleNameAttribute(): string
    {
        $role = $this->role ?? '';

        return $role === '' ? '—' : ucfirst(str_replace('_', ' ', $role));
    }

    /**
     * Whether the user can access the admin area (dashboard + tool management).
     * Owner and Admin roles have access; Developer/QA/User do not.
     */
    public function canAccessAdminArea(): bool
    {
        if (! $this->role) {
            return (bool) $this->is_admin;
        }

        return $this->role === 'owner' || $this->role === 'admin' || $this->is_admin;
    }

    /**
     * Whether the user can manage users (Owner only).
     */
    public function canManageUsers(): bool
    {
        return ($this->role ?? '') === 'owner';
    }

    /**
     * Whether the user has the Owner role. Used for Gates/Policies and Blade.
     * (Role is stored as string 'owner' in DB; we expose Owner for permission checks.)
     */
    public function isOwner(): bool
    {
        return strtolower($this->role ?? '') === 'owner';
    }

    /**
     * Whether the user has the Admin role (or legacy is_admin flag). Used for Gates/Policies and Blade.
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin' || (bool) $this->is_admin;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'is_active',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
