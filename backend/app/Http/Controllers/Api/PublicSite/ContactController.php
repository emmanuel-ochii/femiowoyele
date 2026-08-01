<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function __invoke(ContactMessageRequest $request): ContactMessageResource
    {
        return new ContactMessageResource(ContactMessage::create($request->validated()));
    }
}
