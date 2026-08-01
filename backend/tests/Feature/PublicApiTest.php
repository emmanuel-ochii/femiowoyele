<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_core_sections(): void
    {
        $this->seed();

        $this->getJson('/api/home')
            ->assertOk()
            ->assertJsonPath('data.hero.title', 'Femi Owoyele')
            ->assertJsonCount(6, 'data.pillars')
            ->assertJsonPath('data.featured.books.0.slug', 'entrusted');
    }

    public function test_research_ideas_can_be_filtered_by_category(): void
    {
        $this->seed();

        $this->getJson('/api/research-ideas?category=enterprise')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'enterprise-as-stewardship')
            ->assertJsonPath('meta.categories.0.slug', 'enterprise');
    }

    public function test_contact_form_validates_and_stores_messages(): void
    {
        $this->postJson('/api/contact', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'subject' => 'Speaking enquiry',
            'message' => 'We would like to invite Femi for a private leadership session with our board.',
            'type' => 'speaking',
        ])->assertSuccessful()
            ->assertJsonPath('data.type', 'speaking');

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'ada@example.com',
            'type' => 'speaking',
        ]);
    }

    public function test_newsletter_subscription_is_idempotent(): void
    {
        $payload = ['email' => 'reader@example.com', 'name' => 'Reader', 'source' => 'footer'];

        $this->postJson('/api/newsletter/subscribe', $payload)->assertSuccessful();
        $this->postJson('/api/newsletter/subscribe', $payload)->assertOk();

        $this->assertSame(1, NewsletterSubscriber::where('email', 'reader@example.com')->count());
    }
}
