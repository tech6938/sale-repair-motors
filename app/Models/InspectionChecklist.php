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

    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function inspectionChecklistResults()
    {
        return $this->hasMany(InspectionChecklistResult::class);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

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
