<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Support\Notifier;

class ContactController extends Controller
{
    public function __invoke(ContactMessageRequest $request): ContactMessageResource
    {
        $message = ContactMessage::create($request->validated());

        Notifier::send(new ContactMessageReceived($message));

        return new ContactMessageResource($message);
    }
}
