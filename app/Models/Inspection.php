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

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function inspectionChecklistResults()
    {
        return $this->hasMany(InspectionChecklistResult::class);
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
