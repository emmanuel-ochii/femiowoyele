<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'attending' => ['required', 'boolean'],
            // Only meaningful for guests who are coming; capped to keep a single
            // submission from claiming the room.
            'guests' => ['nullable', 'integer', 'min:0', 'max:10'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('attending')) {
            $this->merge([
                'attending' => filter_var($this->input('attending'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'attending.required' => 'Please let us know whether you can join us.',
        ];
    }
}
