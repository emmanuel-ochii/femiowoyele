<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Named `enquiry` rather than `message`: Laravel injects its own `$message`
     * (the Illuminate\Mail\Message instance) into every mail view, which would
     * shadow the model.
     */
    public function __construct(public readonly ContactMessage $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Enquiry ({$this->enquiry->type}) — {$this->enquiry->subject}",
            // Replying goes straight back to the sender.
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-message-received');
    }
}
