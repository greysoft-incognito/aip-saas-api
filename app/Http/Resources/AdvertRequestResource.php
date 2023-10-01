<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertRequestResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'line3' => $this->line3,
            'status' => $this->status,
            'image_url' => $this->image_url,
            'duration' => $this->duration,
            'hide_texts' => $this->hide_texts,
            'duration_readable' => now()->addHours($this->duration + 1)->longAbsoluteDiffForHumans(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}