<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InspectionChecklist extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
        'id',
        'inspection_type_id',
        'uuid',
        'title',
        'description',
        'display_order',
        'is_required',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_required' => 'boolean',
    ];

    /**
     * Get the inspection type that owns the InspectionChecklist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class);
    }

    /**
     * Get the checklist items associated with the inspection checklist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

    /**
     * Get the inspection checklist results associated with the inspection checklist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inspectionChecklistResults()
    {
        return $this->hasMany(InspectionChecklistResult::class);
    }

    /**
     * Scope a query to only include required inspection checklists.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to only include optional inspection checklists.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Scope a query to order the inspection checklists by their display order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Determines if the previous checklist for a given vehicle is completed.
     *
     * For the current checklist, this function checks if the checklist with the
     * immediately preceding display order is completed for the specified vehicle.
     * If the current checklist is the first in order, it is considered completed by default.
     *
     * @param \App\Models\Vehicle $vehicle The vehicle to check the previous checklist completion for.
     * @return bool True if the previous checklist is completed, false otherwise.
     */
    public function isPreviousChecklistCompleted(Vehicle $vehicle): bool
    {
        if ($this->display_order <= 1) {
            return true;
        }

        $previousChecklist = InspectionChecklist::whereInspectionTypeId($this->inspectionType->id)
            ->where('display_order', $this->display_order - 1)
            ->with(['inspectionChecklistResults' => function ($query) use ($vehicle) {
                $query->whereHas('inspection.vehicle', fn($q) => $q->where('id', $vehicle->id));
            }])
            ->first();

        if (empty($previousChecklist)) {
            return false;
        }

        // Get the result specifically for this vehicle
        $result = $previousChecklist->inspectionChecklistResults->first();

        return $result?->status === InspectionChecklistResult::STATUS_COMPLETED;
    }
}
