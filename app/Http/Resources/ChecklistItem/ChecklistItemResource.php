<?php

namespace App\Http\Resources\ChecklistItem;

use Illuminate\Http\Request;
use App\Models\InspectionChecklistResult;
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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
