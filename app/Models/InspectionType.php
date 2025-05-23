<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionType extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
        'id',
        'uuid',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function inspectionChecklists()
    {
        return $this->hasMany(InspectionChecklist::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
