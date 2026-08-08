<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\PickupPoint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Receipt sent to the buyer, including where to collect the book. */
class OrderReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        $replyTo = config('mail.notifications.to');

        return new Envelope(
            subject: "Your pre-order of Entrusted — {$this->order->reference}",
            replyTo: filled($replyTo) ? [new Address($replyTo)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-receipt',
            with: ['pickupPoints' => PickupPoint::active()->get()],
        );
    }
}
