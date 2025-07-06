<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory, HasUuid, Timestamps;

    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'id',
        'vehicle_id',
        'inspection_type_id',
        'uuid',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the vehicle associated with the inspection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the inspection type associated with the inspection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class);
    }

    /**
     * Get the inspection checklist results associated with the inspection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inspectionChecklistResults()
    {
        return $this->hasMany(InspectionChecklistResult::class);
    }

    /**
     * Scope a query to only include completed inspections.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include inspections with incomplete status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', self::STATUS_INCOMPLETE);
    }

    /**
     * Marks the inspection as completed.
     *
     * @return void
     */
    public function markAsComplete()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Determines if the inspection has been completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Determines if the inspection is incomplete.
     *
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return $this->status === self::STATUS_INCOMPLETE;
    }
}
