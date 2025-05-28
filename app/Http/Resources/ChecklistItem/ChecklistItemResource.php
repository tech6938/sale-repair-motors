<?php

namespace App\Http\Resources\ChecklistItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'item_type' => $this->item_type,
            'is_required' => $this->is_required,
            'min' => $this->min,
            'max' => $this->max,
            'created_at' => $this->createdAt(),
            'updated_at' => strip_tags($this->updatedAt()),
            'itemOptions' => $this->itemOptions?->map(function ($itemOption) {
                return [
                    'id' => $itemOption->uuid,
                    'label' => $itemOption->label,
                    'created_at' => $this->createdAt(),
                    'updated_at' => strip_tags($this->updatedAt()),
                ];
            })->toArray(),
            'results' => $this->checklistItemResults?->map(function ($result) {
                return [
                    'id' => $result->uuid,
                    'value' => $result->formattedValue,
                    'created_at' => $result->createdAt(),
                    'updated_at' => strip_tags($result->updatedAt()),
                ];
            })->toArray(),
        ];
    }
}
