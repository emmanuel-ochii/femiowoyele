<?php

namespace App\Support;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Notifier
{
    /**
     * Delivers an internal notification to the configured recipients.
     *
     * Sending is deliberately best-effort: a guest's RSVP or enquiry is already
     * safely stored by the time this runs, so an email provider outage must be
     * logged rather than surfaced as a failed submission.
     */
    public static function send(Mailable $mailable): bool
    {
        $to = config('mail.notifications.to');

        if (blank($to)) {
            Log::warning('Notification not sent: mail.notifications.to is not configured.', [
                'mailable' => $mailable::class,
            ]);

            return false;
        }

        try {
            Mail::to($to)
                ->cc(config('mail.notifications.cc', []))
                ->send($mailable);

            return true;
        } catch (Throwable $exception) {
            Log::error('Notification email failed to send.', [
                'mailable' => $mailable::class,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
