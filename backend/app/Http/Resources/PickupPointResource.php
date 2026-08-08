<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'contact_phone' => $this->contact_phone,
            'opening_hours' => $this->opening_hours,
            'note' => $this->note,
            // Must be returned: the admin form round-trips whatever it receives,
            // so omitting these silently deactivates and re-orders the record.
            'is_active' => (bool) $this->is_active,
            'order' => (int) $this->order,
        ];
    }
}
