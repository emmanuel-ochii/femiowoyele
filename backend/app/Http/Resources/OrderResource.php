<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $canManageContent = $request->user()?->can('manage-content') ?? false;
        $book = $this->resource->relationLoaded('book') ? $this->book : null;
        $pickupPoint = $this->resource->relationLoaded('pickupPoint') ? $this->pickupPoint : null;

        return [
            'id' => $this->when($canManageContent, $this->id),
            'reference' => $this->reference,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'quantity' => $this->quantity,
            'unit_amount' => $this->unit_amount,
            'total_amount' => $this->total_amount,
            'unit_display' => $this->formattedUnit(),
            'total_display' => $this->formattedTotal(),
            'currency' => $this->currency,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'payment_provider' => $this->payment_provider,
            'created_at' => $this->when($canManageContent, $this->created_at?->toIso8601String()),
            'paid_at_display' => $this->when($canManageContent, $this->displayDateTime($this->paid_at)),
            'created_at_display' => $this->when($canManageContent, $this->displayDateTime($this->created_at)),
            'book_title' => $this->when($canManageContent, $book?->title),
            'pickup_point_name' => $this->when($canManageContent, $pickupPoint?->name),
            'pickup_point_address' => $this->when($canManageContent, $pickupPoint?->address),
            'pickup_point_city' => $this->when($canManageContent, $pickupPoint?->city),
            'book' => new BookResource($this->whenLoaded('book')),
            'pickup_point' => new PickupPointResource($this->whenLoaded('pickupPoint')),
        ];
    }

    private function displayDateTime(mixed $value): ?string
    {
        return $value?->timezone(config('app.timezone'))->format('j M Y, g:ia');
    }
}
