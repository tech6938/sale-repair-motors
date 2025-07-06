<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory, HasUuid, Timestamps;

    const FUEL_TYPE_GASOLINE = 'gasoline';
    const FUEL_TYPE_DIESEL = 'diesel';
    const FUEL_TYPE_ELECTRIC = 'electric';
    const FUEL_TYPE_HYBRID = 'hybrid';

    protected $fillable = [
        'id',
        'user_id',
        'uuid',
        'make',
        'model',
        'year',
        'fuel_type',
        'color',
        'milage',
        'registration',
        'mechanical_fault',
        'bodywork_damage',
    ];

    protected $casts = [
        'year' => 'integer',
        'milage' => 'float',
    ];

    /**
     * The user that owns the Vehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The inspections that belong to the Vehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Scope a query to only include vehicles that the authenticated user has access to.
     *
     * If the authenticated user is a staff user, this scope will filter the query to
     * only include vehicles that the authenticated user owns (i.e. the user ID matches
     * the authenticated user's ID).
     *
     * If the authenticated user is an admin user, this scope will filter the query to
     * only include vehicles that the authenticated user has access to through their
     * managed users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyRoleFilter(Builder $query): Builder
    {
        return $query->when(
            auth()->user()->isStaff(),
            fn($query) => $query->where('user_id', auth()->user()->id)
        )->when(
            auth()->user()->isAdmin(),
            fn($query) => $query->whereIn('user_id', auth()->user()->users()->pluck('id')->toArray())
        );
    }

    /**
     * Scope a query to filter the results by a search query string.
     *
     * Applies a where clause to the query that searches for the given search
     * query string in the following columns: make, model, year, fuel_type, color,
     * milage, and registration.
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
                    $query->orWhere('make', 'like', "%$word%")
                        ->orWhere('model', 'like', "%$word%")
                        ->orWhere('year', 'like', "%$word%")
                        ->orWhere('fuel_type', 'like', "%$word%")
                        ->orWhere('color', 'like', "%$word%")
                        ->orWhere('milage', 'like', "%$word%")
                        ->orWhere('registration', 'like', "%$word%");
                }
            });
        }

        return $query;
    }

    /**
     * Determine if the vehicle has a completed inspection.
     *
     * @return bool
     */
    public function hasCompletedInspection(): bool
    {
        return $this->inspections()->where('status', Inspection::STATUS_COMPLETED)->exists();
    }
}
