<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var array{user:mixed, token:string} $this->resource */
        $payload = $this->resource;

        return [
            'user' => new UserResource($payload['user']),
            'token' => $payload['token'],
        ];
    }
}

