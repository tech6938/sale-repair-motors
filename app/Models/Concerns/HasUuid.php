<?php

namespace App\Models\Concerns;

trait HasUuid
{
    /**
     * Listen to the creating event of the model and set the uuid if it is not provided.
     *
     * This will ensure that all models created have a UUID, even if it is not explicitly
     * set during creation. This is useful for models that are created by the system,
     * rather than by the user.
     */
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
