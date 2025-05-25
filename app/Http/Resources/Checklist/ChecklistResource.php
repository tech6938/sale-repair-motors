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
            'status' => $this->inspection_checklist_results
                ? InspectionChecklistResult::STATUS_COMPLETED
                : InspectionChecklistResult::STATUS_INCOMPLETE,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
