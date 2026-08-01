<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterSubscriberRequest;
use App\Http\Resources\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function __invoke(NewsletterSubscriberRequest $request): NewsletterSubscriberResource
    {
        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->validated('email')],
            [
                'name' => $request->validated('name'),
                'source' => $request->validated('source', 'website'),
            ],
        );

        return new NewsletterSubscriberResource($subscriber);
    }
}
