<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'avatars' => [
                'thumbnail' => $this->avatar_thumbnail_url,
                'full' => $this->avatar_url,
            ],
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'role' => $this->isAdmin() ? User::ROLE_ADMIN : User::ROLE_STAFF,
        ];
    }
}
