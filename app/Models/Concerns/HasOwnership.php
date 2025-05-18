<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasOwnership
{
    protected static function bootHasOwnership()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->owner_id = auth()->user()->id;
            }
        });
    }

    public function scopeOwnedByUser(Builder $query): Builder
    {
        return $query->when(
            !auth()->user()->isSuperAdmin(),
            fn($query) => $query->where('owner_id', auth()->user()->id)
        );
    }
}
