<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionChecklistResult extends Model
{
    use HasFactory;

    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'inspection_id',
        'inspection_checklist_id',
        'status',
    ];

    /**
     * Get the inspection that owns the InspectionChecklistResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    /**
     * Get the inspection checklist that owns the InspectionChecklistResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inspectionChecklist()
    {
        return $this->belongsTo(InspectionChecklist::class);
    }

    /**
     * Get the checklist item results associated with the inspection checklist result.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklistItemResults()
    {
        return $this->hasMany(ChecklistItemResult::class);
    }

    /**
     * Scope a query to only include completed inspection checklist results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include incomplete inspection checklist results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', self::STATUS_INCOMPLETE);
    }

    /**
     * Marks the inspection checklist result as completed.
     *
     * Updates the status to completed and sets the completion timestamp.
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
     * Determines if the inspection checklist result is completed.
     *
     * @return bool True if the status is completed, false otherwise.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Determines if the inspection checklist result is incomplete.
     *
     * @return bool True if the status is incomplete, false otherwise.
     */
    public function isIncomplete(): bool
    {
        return $this->status === self::STATUS_INCOMPLETE;
    }
}
