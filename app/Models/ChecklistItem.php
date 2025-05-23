<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistItem extends Model
{
    use HasFactory, SoftDeletes, HasUuid, Timestamps;

    public const ITEM_TYPE_IMAGE = 'image';
    public const ITEM_TYPE_VIDEO = 'video';
    public const ITEM_TYPE_TEXT = 'text';
    public const ITEM_TYPE_NUMBER = 'number';
    public const ITEM_TYPE_BOOLEAN = 'boolean';
    public const ITEM_TYPE_SELECT = 'select';
    public const ITEM_TYPE_MULTISELECT = 'multiselect';

    protected $fillable = [
        'id',
        'inspection_checklist_id',
        'uuid',
        'title',
        'description',
        'item_type',
        'display_order',
        'is_required',
        'min',
        'max',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_required' => 'boolean',
        'min' => 'integer',
        'max' => 'integer',
    ];

    public function inspectionChecklist()
    {
        return $this->belongsTo(InspectionChecklist::class);
    }

    public function itemOptions()
    {
        return $this->hasMany(ItemOption::class);
    }

    public function checklistItemResults()
    {
        return $this->hasMany(ChecklistItemResult::class);
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
