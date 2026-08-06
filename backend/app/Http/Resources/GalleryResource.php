<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'media_items' => MediaItemResource::collection($this->whenLoaded('mediaItems')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
