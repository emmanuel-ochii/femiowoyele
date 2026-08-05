<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTest extends Command
{
    protected $signature = 'mail:test
                            {--to= : Override the recipient (defaults to mail.notifications.to)}
                            {--from= : Override the From address, e.g. onboarding@resend.dev}';

    protected $description = 'Send a test notification email and report the provider response';

    /**
     * Notification sends are best-effort in the request path, so a misconfigured
     * provider fails silently into the log. This surfaces the provider's actual
     * error on the console instead.
     */
    public function handle(): int
    {
        if ($from = $this->option('from')) {
            config(['mail.from.address' => $from]);
        }

        $to = $this->option('to') ?: config('mail.notifications.to');
        $cc = $this->option('to') ? [] : config('mail.notifications.cc', []);

        $this->line('');
        $this->components->twoColumnDetail('<fg=gray>Mailer</>', config('mail.default'));
        $this->components->twoColumnDetail('<fg=gray>From</>', (string) config('mail.from.address'));
        $this->components->twoColumnDetail('<fg=gray>To</>', (string) $to);
        $this->components->twoColumnDetail('<fg=gray>Cc</>', $cc ? implode(', ', (array) $cc) : '—');
        $this->line('');

        if (blank($to)) {
            $this->components->error('No recipient. Set MAIL_NOTIFY_TO or pass --to=.');

            return self::FAILURE;
        }

        try {
            Mail::raw('Test email from femiowoyele.com. If you are reading this, delivery is working.', function ($message) use ($to, $cc) {
                $message->to($to)->subject('femiowoyele.com — mail test');

                if ($cc) {
                    $message->cc($cc);
                }
            });
        } catch (Throwable $exception) {
            $this->components->error('Send failed.');
            $this->line("  <fg=red>{$exception->getMessage()}</>");
            $this->line('');

            if (str_contains($exception->getMessage(), 'not verified')) {
                $this->components->warn('Verify the sending domain at https://resend.com/domains, then set MAIL_FROM_ADDRESS to an address on it.');
            }

            return self::FAILURE;
        }

        $this->components->info('Sent. Check the inbox (and spam) for the recipients above.');

        return self::SUCCESS;
    }
}
