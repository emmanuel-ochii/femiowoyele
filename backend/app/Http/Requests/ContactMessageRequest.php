<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactMessageRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'type' => [
                'required',
                'string',
                Rule::in(['speaking', 'consulting', 'research', 'partnership', 'media', 'mentorship', 'books', 'general']),
            ],
        ];
    }
}
