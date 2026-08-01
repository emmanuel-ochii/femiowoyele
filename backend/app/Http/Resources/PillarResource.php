<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PillarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'order' => $this->order,
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'media_items' => MediaItemResource::collection($this->whenLoaded('mediaItems')),
        ];
    }
}
