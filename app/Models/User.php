<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Models\Concerns\HasUuid;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\Timestamps;
use App\Models\Concerns\HasOwnership;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\OtpEmailNotification;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, HasUuid, Timestamps, HasOwnership;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';

    protected $fillable = [
        'uuid',
        'owner_id',

        // Personal info
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'address',

        // System info
        'status',
        'admin_comments',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function ownerships(): HasMany
    {
        return $this->hasMany(User::class, 'owner_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getAvatarThumbnailUrlAttribute()
    {
        return $this->avatar ? Storage::disk('public')->url('thumbnails/' . $this->avatar) : null;
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            return '<span class="badge bg-success">Active</span>';
        }

        if ($this->status == self::STATUS_SUSPENDED) {
            return '<span class="badge bg-danger">Suspended</span>';
        }

        return '<span class="badge">Unknown</span>';
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSuspended(Builder $query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeSuperAdmin(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', self::ROLE_SUPER_ADMIN));
    }

    public function scopeAdmin(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', self::ROLE_ADMIN));
    }

    public function scopeStaff(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', self::ROLE_STAFF));
    }

    public function scopeNotSuperAdmin(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->whereIn('name', [self::ROLE_ADMIN, self::ROLE_STAFF]));
    }

    public function scopeApplyFilters(Builder $query)
    {
        if (request()->has('search')) {
            $search = trim(request()->input('search'));
            $keywords = explode(' ', $search);

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhere('name', 'like', "%$word%")
                        ->orWhere('email', 'like', "%$word%")
                        ->orWhere('phone', 'like', "%$word%")
                        ->orWhere('address', 'like', "%$word%")
                        ->orWhere('status', 'like', "%$word%");
                }
            });
        }

        return $query;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isStaff()
    {
        return $this->hasRole(self::ROLE_STAFF);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function sendOtpEmailNotification($otp)
    {
        $this->notify(new OtpEmailNotification($otp));
    }
}
