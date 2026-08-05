<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Mail\RsvpSubmitted;
use App\Models\Rsvp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.notifications.to' => 'faithdolapo27@gmail.com',
            'mail.notifications.cc' => ['profemative@gmail.com'],
        ]);
    }

    public function test_an_rsvp_notifies_the_configured_recipients(): void
    {
        Mail::fake();

        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
            'guests' => 2,
        ])->assertCreated();

        Mail::assertSent(RsvpSubmitted::class, function (RsvpSubmitted $mail) {
            return $mail->hasTo('faithdolapo27@gmail.com')
                && $mail->hasCc('profemative@gmail.com')
                // Replies reach the guest, not the site inbox.
                && $mail->hasReplyTo('ada@example.com')
                && $mail->rsvp->name === 'Ada Builder'
                && $mail->wasUpdated === false;
        });
    }

    public function test_an_updated_rsvp_is_flagged_as_such_in_the_notification(): void
    {
        Mail::fake();
        $payload = ['name' => 'Ada Builder', 'email' => 'ada@example.com', 'attending' => true];

        $this->postJson('/api/rsvp', $payload)->assertCreated();
        $this->postJson('/api/rsvp', [...$payload, 'attending' => false])->assertOk();

        Mail::assertSent(RsvpSubmitted::class, fn (RsvpSubmitted $mail) => $mail->wasUpdated === true);
        Mail::assertSent(RsvpSubmitted::class, 2);
    }

    public function test_a_contact_enquiry_notifies_the_configured_recipients(): void
    {
        Mail::fake();

        $this->postJson('/api/contact', [
            'name' => 'Chidi Nwosu',
            'email' => 'chidi@example.com',
            'subject' => 'Keynote enquiry',
            'type' => 'speaking',
            'message' => 'We would like to invite Femi to keynote our summit in October.',
        ])->assertCreated();

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('faithdolapo27@gmail.com')
                && $mail->hasCc('profemative@gmail.com')
                && $mail->hasReplyTo('chidi@example.com');
        });
    }

    public function test_a_mail_failure_never_costs_the_guest_their_rsvp(): void
    {
        // Simulate the provider being down: the submission must still succeed
        // and the answer must still be stored.
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('Resend unavailable'));

        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('rsvps', ['email' => 'ada@example.com', 'attending' => true]);
        $this->assertSame(1, Rsvp::count());
    }

    public function test_nothing_is_sent_when_no_recipient_is_configured(): void
    {
        Mail::fake();
        config(['mail.notifications.to' => null]);

        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
        ])->assertCreated();

        Mail::assertNothingSent();
    }
}
