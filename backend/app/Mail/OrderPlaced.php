<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Internal notification that a pre-order has been paid for. */
class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pre-order — {$this->order->name} ({$this->order->quantity} × Entrusted)",
            replyTo: [new Address($this->order->email, $this->order->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.order-placed');
    }
}
