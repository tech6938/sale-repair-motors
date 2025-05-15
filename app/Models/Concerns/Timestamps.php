<?php

namespace App\Models\Concerns;

trait Timestamps
{
    protected static function bootTimestamps(): void
    {
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }

    public function createdAt($isHumanTime = true): string
    {
        return $isHumanTime
            ? humanTime($this->created_at)
            : frontendDateTime($this->created_at);
    }

    public function updatedAt($isHumanTime = true): string
    {
        if (empty($this->updated_at)) return '<small><i>Never Updated</i></small>';

        return $isHumanTime
            ? humanTime($this->updated_at)
            : frontendDateTime($this->updated_at);
    }
}
