<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\RsvpRequest;
use App\Http\Resources\RsvpResource;
use App\Mail\RsvpConfirmation;
use App\Mail\RsvpSubmitted;
use App\Models\Rsvp;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    /**
     * Records an RSVP for the launch evening.
     *
     * Keyed on email so a guest who changes their mind updates their answer
     * instead of creating a second, contradictory row — something the Google
     * Form this replaces could not do.
     */
    public function __invoke(RsvpRequest $request): JsonResponse
    {
        $data = $request->validated();
        $attending = (bool) $data['attending'];

        $rsvp = Rsvp::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'attending' => $attending,
                'guests' => $attending ? (int) ($data['guests'] ?? 0) : 0,
                'note' => $data['note'] ?? null,
                'event_slug' => 'home.launch',
                'source' => 'website',
            ],
        );

        $wasUpdated = ! $rsvp->wasRecentlyCreated;

        Notifier::send(new RsvpSubmitted($rsvp, $wasUpdated));
        Notifier::sendTo($rsvp->email, new RsvpConfirmation($rsvp));

        return response()->json([
            'data' => new RsvpResource($rsvp),
            'meta' => ['updated' => $wasUpdated],
        ], $rsvp->wasRecentlyCreated ? 201 : 200);
    }
}
