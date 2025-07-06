<?php

namespace App\Models\Concerns;

trait Timestamps
{
    /**
     * When creating a new model, set updated_at to null.
     *
     * This is because the model will not be updated until it is saved, so there is no
     * update time to set. This ensures that the updated_at column is not set until
     * the model is actually updated.
     */
    protected static function bootTimestamps(): void
    {
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }

    /**
     * Return the creation time of the model, either in human time or formatted for frontend display.
     *
     * @param bool $isHumanTime Whether to return the time in human time or formatted for frontend display.
     * @return string The formatted time.
     */
    public function createdAt($isHumanTime = true): string
    {
        return $isHumanTime
            ? humanTime($this->created_at)
            : frontendDateTime($this->created_at);
    }

    /**
     * Return the update time of the model, either in human time or formatted for frontend display.
     *
     * @param bool $isHumanTime Whether to return the time in human time or formatted for frontend display.
     * @return string The formatted time.
     */
    public function updatedAt($isHumanTime = true): string
    {
        if (empty($this->updated_at)) return '<small><i>Never Updated</i></small>';

        return $isHumanTime
            ? humanTime($this->updated_at)
            : frontendDateTime($this->updated_at);
    }
}
