<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray(Request $request): array
    {
        return [
            'status' => 'error',
            'message' => $this['message'],
            'errors' => $this['errors'] ?? null,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this['status_code'] ?? 400);
    }
}
