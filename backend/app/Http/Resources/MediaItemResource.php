<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pillar_id' => $this->pillar_id,
            'type' => $this->type,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'thumbnail_path' => $this->thumbnail_path,
            'published_at' => $this->published_at?->toDateString(),
            'pillar' => new PillarResource($this->whenLoaded('pillar')),
        ];
    }
}
