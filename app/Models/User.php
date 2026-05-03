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
        'phone',
        'gender',
        'profile_image',
        'role',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // ── Profile image URL accessor ───────────────────────────────────────────
    // Usage in blade: {{ $user->profile_image_url }}
    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image && file_exists(public_path($this->profile_image))) {
            return asset($this->profile_image);
        }
        return asset('assets/admin/images/default-avatar.png');
    }

    // ── Relations ────────────────────────────────────────────────────────────
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
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
