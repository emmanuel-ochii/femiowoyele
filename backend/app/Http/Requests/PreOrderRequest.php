<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('payments.max_quantity', 20)],
            // Optional at checkout: buyers can also choose on collection.
            'pickup_point_id' => ['nullable', 'integer', 'exists:pickup_points,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.max' => 'For larger orders please contact us directly.',
        ];
    }
}
