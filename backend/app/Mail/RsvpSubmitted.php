<?php

namespace App\Mail;

use App\Models\Rsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Rsvp $rsvp,
        public readonly bool $wasUpdated = false,
    ) {}

    public function envelope(): Envelope
    {
        $verb = $this->wasUpdated
            ? 'updated their RSVP'
            : ($this->rsvp->attending ? 'is attending' : 'cannot attend');

        return new Envelope(
            subject: "RSVP — {$this->rsvp->name} {$verb}",
            // Replying goes straight back to the guest rather than to the site.
            replyTo: [new Address($this->rsvp->email, $this->rsvp->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.rsvp-submitted');
    }
}
