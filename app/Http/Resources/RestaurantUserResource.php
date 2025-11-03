<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'mobile_number' => $this->mobile_number,
            'total_users_count' => $this->total_users_count,
            'added_by' => [
                'id' => $this->addedBy->id,
                'name' => $this->addedBy->name,
                'email' => $this->addedBy->email,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
