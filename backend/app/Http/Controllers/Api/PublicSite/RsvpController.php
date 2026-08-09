<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\RsvpRequest;
use App\Http\Resources\RsvpResource;
use App\Mail\RsvpConfirmation;
use App\Mail\RsvpSubmitted;
use App\Models\ContentBlock;
use App\Models\Rsvp;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RsvpController extends Controller
{
    /**
     * Records an RSVP for the launch evening.
     *
     * Keyed on email so a guest who changes their mind updates their answer
     * instead of creating a second, contradictory row — something the Google
     * Form this replaces could not do.
     */
    /**
     * Refuses submissions once the RSVP deadline has passed.
     *
     * Enforced here rather than only in the UI: the closed state must hold for
     * a direct API call, a stale open tab, or a cached page.
     */
    private function guardDeadline(): void
    {
        $closesAt = ContentBlock::where('slug', 'home.launch')->value('meta')['rsvp_closes_at'] ?? null;

        if (blank($closesAt) || Carbon::now()->lessThanOrEqualTo(Carbon::parse($closesAt))) {
            return;
        }

        throw ValidationException::withMessages([
            'attending' => 'RSVPs for this event have now closed. Please get in touch if you still hope to attend.',
        ]);
    }

    public function __invoke(RsvpRequest $request): JsonResponse
    {
        $this->guardDeadline();

        $data = $request->validated();
        $attending = (bool) $data['attending'];

        $rsvp = Rsvp::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'attending' => $attending,
                'guests' => 0,
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
