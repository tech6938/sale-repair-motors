<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\Timestamps;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\OtpEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordResetNotification;
use App\Notifications\InvitationEmailNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, HasUuid, Timestamps;

    public const STATUS_INVITED = 'invited';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';

    protected $fillable = [
        'uuid',

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
        if ($this->status == self::STATUS_INVITED) {
            return '<span class="badge bg-warning">Invited</span>';
        }

        if ($this->status == self::STATUS_ACTIVE) {
            return '<span class="badge bg-success">Active</span>';
        }

        if ($this->status == self::STATUS_SUSPENDED) {
            return '<span class="badge bg-danger">Suspended</span>';
        }

        return '<span class="badge">Unknown</span>';
    }

    public function getEmailVerificationBadgeAttribute()
    {
        return !empty($this->email_verified_at)
            ? '<span class="badge bg-success">Yes</span>'
            : '<span class="badge bg-danger">No</span>';
    }

    public function scopeInvited(Builder $query)
    {
        return $query->where('status', self::STATUS_INVITED);
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
        return $query->whereHas('roles', fn($q) => $q->whereIn('name', self::getRoles()));
    }

    public function isInvited()
    {
        return $this->status === self::STATUS_INVITED;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function hasCompleteProfile()
    {
        return $this->status !== self::STATUS_INVITED;
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

    public static function getStatuses(?string $separator = '|')
    {
        if (!$separator) {
            return [self::STATUS_INVITED, self::STATUS_ACTIVE, self::STATUS_SUSPENDED];
        }

        return implode($separator, [self::STATUS_INVITED, self::STATUS_ACTIVE, self::STATUS_SUSPENDED]);
    }

    public static function getRoles(?string $separator = null, bool $includeSuperAdmin = false)
    {
        $roles = [self::ROLE_ADMIN, self::ROLE_STAFF];

        if ($includeSuperAdmin) {
            $roles[] = self::ROLE_SUPER_ADMIN;
        }

        return $separator ? implode($separator, $roles) : $roles;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    public function sendInvitationEmailNotification()
    {
        $this->notify(new InvitationEmailNotification());
    }

    public function sendOtpEmailNotification($otp)
    {
        $this->notify(new OtpEmailNotification($otp));
    }
}
