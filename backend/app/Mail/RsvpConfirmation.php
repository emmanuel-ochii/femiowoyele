<?php

namespace App\Mail;

use App\Models\ContentBlock;
use App\Models\Rsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Acknowledgement sent to the guest who submitted an RSVP. Distinct from
 * RsvpSubmitted, which notifies the team.
 */
class RsvpConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Rsvp $rsvp) {}

    public function envelope(): Envelope
    {
        $replyTo = config('mail.notifications.to');

        return new Envelope(
            subject: 'Thank You for Your RSVP',
            // Sent from a no-reply domain, so point replies at the organiser.
            replyTo: filled($replyTo) ? [new Address($replyTo)] : [],
        );
    }

    public function content(): Content
    {
        // Event details come from the `home.launch` content block so the email
        // never drifts from what the site and the CMS are showing.
        $meta = ContentBlock::where('slug', 'home.launch')->value('meta') ?? [];

        return new Content(
            view: 'mail.rsvp-confirmation',
            with: [
                'body' => self::messageFor((bool) $this->rsvp->attending),
                'details' => array_filter([
                    'Date' => $meta['date_label'] ?? null,
                    'Time' => $meta['time_label'] ?? null,
                    'Venue' => $meta['venue'] ?? null,
                    'Address' => $meta['address'] ?? null,
                ]),
            ],
        );
    }

    public static function messageFor(bool $attending): string
    {
        return $attending
            ? 'Thank you for confirming your attendance. I look forward to celebrating this special milestone with you and truly appreciate your time and presence.'
            : 'Thank you for letting me know. Although I will miss celebrating with you in person, I sincerely appreciate your kind consideration and wish you all the best.';
    }
}
