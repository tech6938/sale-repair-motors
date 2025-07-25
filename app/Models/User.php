<?php

namespace App\Models;

use App\Services\FcmService;
use App\Models\Concerns\HasUuid;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\Timestamps;
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
    use HasFactory, Notifiable, HasRoles, HasApiTokens, HasUuid, Timestamps;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_PREPARATION_MANAGER = 'preparation_manager';
    public const ROLE_PREPARATION_STAFF = 'preparation_staff';

    protected $fillable = [
        'uuid',
        'user_id',

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
        'fcm_token',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Boot the model and add a creating event listener to set the user_id
     * of the model being created to the currently authenticated user's id.
     */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->user_id = auth()->user()->id;
            }
        });
    }

    /**
     * The settings that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Get the users that are managed by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the user who manages this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vehicles that are managed by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the URL for the user's avatar thumbnail.
     *
     * If the user has an avatar, the URL to the thumbnail version will be returned.
     * If the user does not have an avatar, null will be returned.
     *
     * @return string|null The URL of the avatar thumbnail or null if not available.
     */
    public function getAvatarThumbnailUrlAttribute()
    {
        return $this->avatar ? Storage::disk('public')->url('thumbnails/' . $this->avatar) : null;
    }

    /**
     * Get the URL for the user's avatar.
     *
     * If the user has an avatar, the URL to the avatar will be returned.
     * If the user does not have an avatar, null will be returned.
     *
     * @return string|null The URL of the avatar or null if not available.
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }

    /**
     * Get the status badge attribute for the user.
     *
     * Returns an HTML string representing the user's status as a badge. The badge
     * is styled according to the status: "Active" is represented with a green
     * badge, "Suspended" with a red badge, and any other status with a default
     * badge labeled "Unknown".
     *
     * @return string The HTML string containing the status badge.
     */
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

    /**
     * Scope a query to only include users that the authenticated user has access to.
     *
     * If the authenticated user is a super admin, this scope does not apply any
     * filters to the query.
     *
     * If the authenticated user is not a super admin, this scope will filter the
     * query to only include users that the authenticated user "owns" (i.e. the
     * user ID matches the authenticated user's ID).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyRoleFilter(Builder $query): Builder
    {
        return $query->when(
            !auth()->user()->isSuperAdmin(),
            fn($query) => $query->where('user_id', auth()->user()->id)
        );
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include suspended users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended(Builder $query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope a query to only include super admin users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuperAdmin(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', self::ROLE_SUPER_ADMIN));
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', self::ROLE_ADMIN));
    }

    /**
     * Scope a query to only include staff users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStaff(Builder $query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', [
            self::ROLE_STAFF,
        ]));
    }

    public function scopePreprationManager(Builder $query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', self::ROLE_PREPARATION_MANAGER);
        });
    }

    public function scopePreprationStaff(Builder $query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', self::ROLE_PREPARATION_STAFF);
        });
    }

    /**
     * Scope a query to only include users that are not super admins.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotSuperAdmin(Builder $query)
    {
        return $query->whereDoesntHave(
            'roles',
            fn($q) =>
            $q->where('name', self::ROLE_SUPER_ADMIN)
        );
    }


    /**
     * Scope a query to filter the results by a search query string.
     *
     * Applies a where clause to the query that searches for the given search
     * query string in the following columns: name, email, phone, address, and
     * status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyRequestFilters(Builder $query)
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

    /**
     * Determine if the user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Determine if the user is suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Determine if the user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isPreparationManager()
    {
        return $this->hasRole(self::ROLE_PREPARATION_MANAGER);
    }

    public function isPreparationStaff()
    {
        return $this->hasRole(self::ROLE_PREPARATION_STAFF);
    }

    /**
     * Determine if the user is a staff member.
     *
     * @return bool True if the user has the staff role, false otherwise.
     */
    public function isStaff()
    {
        return $this->hasRole(self::ROLE_STAFF);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Sends an OTP to the user's email address.
     *
     * @param object $otp
     * @return void
     */
    public function sendOtpEmailNotification($otp)
    {
        $this->notify(new OtpEmailNotification($otp));
    }

    /**
     * Send a Firebase Cloud Messaging (FCM) notification to the user's FCM token.
     *
     * @param string $title The title of the notification.
     * @param string $body The body of the notification.
     * @param array $data Data to be sent with the notification.
     * @return void
     */
    public function sendFirebaseNotification(string $title, string $body, array $data = [])
    {
        if (empty($this->fcm_token)) return;

        (new FcmService())->sendNotification($this->fcm_token, $title, $body, $data);
    }

    /**
     * Updates the user's FCM token in the database.
     *
     * @param string $token The new FCM token.
     * @return void
     */
    public function updateFcmToken(string $token): void
    {
        $this->update(['fcm_token' => $token]);
    }

    public function assignedVehiclesAsManager()
    {
        return $this->hasMany(VehicleAssign::class, 'preparation_manager_id');
    }

    public function assignedVehiclesAsStaff()
    {
        return $this->hasMany(VehicleAssign::class, 'preparation_staff_id');
    }
}
