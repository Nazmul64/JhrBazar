<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'profile_image',
        'role',
        'role_id',
        'status',
        'last_name',
        'national_id_card',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_holder',
        'balance',
        'is_blocked',
        'last_ip',
    ];

    public function transactions()
    {
        return $this->hasMany(SellerTransaction::class, 'seller_id');
    }

    public function orderStatusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'changed_by');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['profile_image_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role constants ───────────────────────────────────────────────────────
    const ROLE_USER        = 'user';
    const ROLE_ADMIN       = 'admin';
    const ROLE_SELLER      = 'seller';
    const ROLE_CUSTOMER    = 'customer';
    const ROLE_MANAGER     = 'manager';
    const ROLE_EMPLOYEE    = 'employee';
    const ROLE_VENDOR      = 'vendor';
    const ROLE_SUBADMIN    = 'subadmin';
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_STAFF       = 'staff';

    // ── Role helpers ─────────────────────────────────────────────────────────
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has a specific permission.
     * Permissions are retrieved via the user's role and direct assignments.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true; // Admin/SuperAdmin has all permissions
        }

        // 1. Check direct permissions
        if ($this->permissions()->where('key', $permission)->exists()) {
            return true;
        }

        // 2. Check role-based permissions
        $role = $this->roleModel;

        // Fallback: If role_id is missing, try to find role by name string
        if (!$role && $this->role) {
            $role = Role::where('user_type', $this->role)->first();
        }

        if (!$role) {
            return false;
        }

        return $role->permissions->contains('key', $permission);
    }

    // ── Profile image URL accessor ───────────────────────────────────────────
    // Usage in blade: {{ $user->profile_image_url }}
    public function getProfileImageUrlAttribute(): string
    {
        if (!$this->profile_image) {
            return asset('assets/admin/images/default-avatar.png');
        }

        // Check if it already has a path or is a full URL
        if (str_starts_with($this->profile_image, 'http')) {
            return $this->profile_image;
        }

        $path = $this->profile_image;
        if (!str_contains($path, '/')) {
            $path = 'uploads/profile_images/' . $path;
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('assets/admin/images/default-avatar.png');
    }

    // ── Relations ────────────────────────────────────────────────────────────
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'employee_permissions', 'employee_id', 'permission_id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }
}
