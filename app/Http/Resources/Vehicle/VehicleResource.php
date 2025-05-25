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
            'address' => $this->address,
            'color' => $this->color,
            'price' => currency($this->price),
            'license_plate' => $this->license_plate,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'inspections' => $this->inspections?->map(function ($inspection) {
                return [
                    'id' => $inspection->uuid,
                    'title' => $inspection->title,
                    'created_at' => $inspection->created_at->toDateTimeString(),
                    'updated_at' => $inspection->updated_at?->toDateTimeString(),
                ];
            }),
        ];
    }
}
