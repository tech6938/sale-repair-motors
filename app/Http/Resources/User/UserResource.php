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
        if ($this->isAdmin()) {
    $role = User::ROLE_ADMIN;
} elseif ($this->isStaff()) {
    $role = User::ROLE_STAFF;
} elseif ($this->isPreparationManager()) {
    $role = User::ROLE_PREPARATION_MANAGER;
} elseif ($this->isPreparationStaff()) {
    $role = User::ROLE_PREPARATION_STAFF;
} else {
    $role = '';
}

        $data = [
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
            'created_at' => $this->createdAt(),
            'updated_at' => strip_tags($this->updatedAt()),
            'role' => $role,
        ];

        if (auth()->user()->isAdmin()) {
            $data['admin_comments'] = $this->admin_comments;
        }

        return $data;
    }
}
