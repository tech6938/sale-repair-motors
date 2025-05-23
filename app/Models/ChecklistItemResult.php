<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItemResult extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
        'id',
        'inspection_checklist_result_id',
        'checklist_item_id',
        'uuid',
        'value',
        'size',
    ];

    protected $casts = [
        'value' => 'array',
        'size' => 'float',
    ];

    public function inspectionChecklistResult()
    {
        return $this->belongsTo(InspectionChecklistResult::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function getFormattedValueAttribute()
    {
        $itemType = $this->checklistItem->item_type;

        if (in_array($itemType, [ChecklistItem::ITEM_TYPE_SELECT, ChecklistItem::ITEM_TYPE_MULTISELECT])) {
            $options = $this->checklistItem->itemOptions->pluck('label', 'value');

            if ($itemType === ChecklistItem::ITEM_TYPE_SELECT) {
                return $options[$this->value] ?? $this->value;
            }

            return collect($this->value)->map(function ($val) use ($options) {
                return $options[$val] ?? $val;
            })->implode(', ');
        }

        return $this->value;
    }
}
