<?php

namespace App\Http\Resources\Checklist;

use Illuminate\Http\Request;
use App\Models\InspectionChecklistResult;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistResource extends JsonResource
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
            'is_required' => $this->is_required,
            'display_order' => $this->display_order,
            'status' => $this->inspectionChecklistResults?->first()?->status === InspectionChecklistResult::STATUS_COMPLETED
                ? 'completed'
                : 'incomplete',
            'created_at' => $this->createdAt(),
            'updated_at' => strip_tags($this->updatedAt()),
        ];
    }
}
