<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'role' => $this->role ? [
                'name' => $this->role->name,
                'label' => $this->role->label ?? $this->role->name,
            ] : null,
        ];
    }
}


