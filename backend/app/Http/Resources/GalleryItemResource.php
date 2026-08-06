<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gallery_id' => $this->gallery_id,
            'media_item_id' => $this->media_item_id,
            'order' => $this->order,
            'gallery' => new GalleryResource($this->whenLoaded('gallery')),
            'media_item' => new MediaItemResource($this->whenLoaded('mediaItem')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
