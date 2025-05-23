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

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function inspectionChecklist()
    {
        return $this->belongsTo(InspectionChecklist::class);
    }

    public function checklistItemResults()
    {
        return $this->hasMany(ChecklistItemResult::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', self::STATUS_INCOMPLETE);
    }

    public function markAsComplete()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isIncomplete(): bool
    {
        return $this->status === self::STATUS_INCOMPLETE;
    }
}
