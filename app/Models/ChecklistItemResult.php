<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

        if ($itemType === ChecklistItem::ITEM_TYPE_NUMBER) {
            return number_format($this->value, 2);
        }

        if ($itemType === ChecklistItem::ITEM_TYPE_IMAGE) {
            return empty($this->value) ? [] : [
                'thumbnail' => Storage::disk('public')->url('thumbnails/' . $this->value),
                'full' => Storage::disk('public')->url($this->value),
            ];
        }

        if ($itemType === ChecklistItem::ITEM_TYPE_MULTI_IMAGE) {
            if (empty($this->value) || !is_array($this->value)) {
                return [];
            }

            $result = [];
            foreach ($this->value as $imgPath) {
                if (empty($imgPath)) continue;

                $result[] = [
                    'thumbnail' => Storage::disk('public')->url('thumbnails/' . $imgPath),
                    'full' => Storage::disk('public')->url($imgPath),
                ];
            }

            return $result;
        }

        if ($itemType === ChecklistItem::ITEM_TYPE_VIDEO) {
            return $this->value ? Storage::disk('public')->url($this->value) : null;
        }

        return $this->value;
    }
}
