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
}
