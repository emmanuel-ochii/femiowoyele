<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
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
}
