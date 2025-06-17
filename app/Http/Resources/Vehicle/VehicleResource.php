<?php

namespace App\Http\Resources\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'title' => implode(' ', [$this->make, $this->model, $this->year]),
            'fuel_type' => $this->fuel_type,
            'color' => $this->color,
            'license_plate' => $this->license_plate,
            'milage' => $this->milage,
            'registration' => $this->registration,
            'mechanical_fault' => (bool) $this->mechanical_fault,
            'bodywork_damage' => (bool) $this->bodywork_damage,
            'created_at' => $this->createdAt(),
            'updated_at' => strip_tags($this->updatedAt()),
            'inspections' => $this->inspections?->map(function ($inspection) {
                return [
                    'id' => $inspection->uuid,
                    'status' => $inspection->status,
                    'started_at' => $inspection->started_at ? frontendDateTime($inspection->started_at) : null,
                    'completed_at' => $inspection->completed_at ? frontendDateTime($inspection->completed_at) : null,
                    'created_at' => $this->createdAt(),
                    'updated_at' => strip_tags($this->updatedAt()),
                ];
            }),
        ];
    }
}
