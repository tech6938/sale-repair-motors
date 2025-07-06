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

    /**
     * Get the inspection checklist result that owns this checklist item result.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inspectionChecklistResult()
    {
        return $this->belongsTo(InspectionChecklistResult::class);
    }

    /**
     * Get the checklist item that owns this checklist item result.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the formatted value attribute based on the checklist item type.
     *
     * Formats the value differently depending on whether the item type is
     * a number, image, multi-image, or video. For numbers, it returns a
     * formatted string with two decimal places. For images, it returns an
     * associative array with URLs for thumbnail and full images. For
     * multi-images, it returns an array of such associative arrays. For
     * videos, it returns the URL of the video. For other types, it returns
     * the value as-is.
     *
     * @return mixed The formatted value based on the item type.
     */
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
