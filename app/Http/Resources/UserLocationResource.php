<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLocationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'fullname' => $this->user->fullname,
            'coordinates' => [
                $this->location->latitude,
                $this->location->longitude,
            ],
            'lat' => $this->location->latitude,
            'lng' => $this->location->longitude,
            'srid' => $this->location->srid,
            'area' => $this->area
        ];
    }
}
