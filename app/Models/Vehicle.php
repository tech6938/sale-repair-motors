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

    protected $fillable = [
        'id',
        'user_id',
        'uuid',
        'make',
        'model',
        'year',
        'fuel_type',
        'address',
        'color',
        'price',
        'license_plate',
    ];

    protected $casts = [
        'year' => 'integer',
        'price' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function scopeManagedByUser(Builder $query): Builder
    {
        return $query->when(
            !auth()->user()->isSuperAdmin(),
            fn($query) => $query->where('user_id', auth()->user()->id)
        );
    }

    public function scopeApplyFilters(Builder $query)
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
                        ->orWhere('address', 'like', "%$word%")
                        ->orWhere('color', 'like', "%$word%")
                        ->orWhere('price', 'like', "%$word%")
                        ->orWhere('license_plate', 'like', "%$word%");
                }
            });
        }

        return $query;
    }

    public function hasCompletedInspection(): bool
    {
        return $this->inspections()->where('status', Inspection::STATUS_COMPLETED)->exists();
    }
}
