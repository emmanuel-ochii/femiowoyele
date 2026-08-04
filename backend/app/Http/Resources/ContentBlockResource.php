<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [
                'id' => null,
                'slug' => null,
                'title' => null,
                'body' => null,
                'context' => null,
                'meta' => [],
                'order' => null,
            ];
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body,
            'context' => $this->context,
            'meta' => $this->meta ?? [],
            'order' => $this->order,
        ];
    }
}
