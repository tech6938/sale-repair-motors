<?php

namespace App\Models\Concerns;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($query) {
            $query->uuid = !empty($query->uuid) ? $query->uuid : getUuid();
        });
    }

    /**
     * Get the route key for the model for route-model binding.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
