<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [
                'id' => null,
                'text' => null,
                'source' => null,
                'is_active' => false,
            ];
        }

        return [
            'id' => $this->id,
            'text' => $this->text,
            'source' => $this->source,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
