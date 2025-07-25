<?php

namespace App\Http\Resources\Preparation_Staff;

use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PreparationStaffCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resource instanceof AbstractPaginator ? [
            'staffs' => $this->collection->map(function ($staff) {
                return $staff->staff ?? [];
            }),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ]
        ] : parent::toArray($request);
    }
}
