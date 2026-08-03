<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * ✅ ไม่ห่อ response ด้วย 'data' key
     * เพราะ API client คาดหวัง { token, user } ที่ root level
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var array{user:mixed, token:string} $payload */
        $payload = $this->resource;

        return [
            'token' => $payload['token'],
            'user' => new UserResource($payload['user']),
        ];
    }
}
