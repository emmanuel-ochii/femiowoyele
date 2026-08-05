<?php

namespace Tests\Feature;

use App\Models\Rsvp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_an_rsvp(): void
    {
        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
            'guests' => 2,
        ])
            ->assertCreated()
            ->assertJsonPath('data.attending', true)
            ->assertJsonPath('data.guests', 2)
            ->assertJsonPath('meta.updated', false);

        $this->assertDatabaseHas('rsvps', ['email' => 'ada@example.com', 'attending' => true]);
    }

    public function test_resubmitting_updates_the_existing_answer_rather_than_duplicating(): void
    {
        $payload = ['name' => 'Ada Builder', 'email' => 'ada@example.com', 'attending' => true, 'guests' => 2];

        $this->postJson('/api/rsvp', $payload)->assertCreated();

        $this->postJson('/api/rsvp', [...$payload, 'attending' => false])
            ->assertOk()
            ->assertJsonPath('data.attending', false)
            // Guests only make sense for someone who is coming.
            ->assertJsonPath('data.guests', 0)
            ->assertJsonPath('meta.updated', true);

        $this->assertSame(1, Rsvp::count());
    }

    public function test_it_requires_an_attendance_answer(): void
    {
        $this->postJson('/api/rsvp', ['name' => 'Ada Builder', 'email' => 'ada@example.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('attending');
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $this->postJson('/api/rsvp', ['name' => 'Ada', 'email' => 'not-an-email', 'attending' => true])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_rsvps_are_listed_in_the_admin_area_with_attendance_counts(): void
    {
        Rsvp::create(['name' => 'Yes One', 'email' => 'y1@example.com', 'attending' => true, 'guests' => 2]);
        Rsvp::create(['name' => 'No One', 'email' => 'n1@example.com', 'attending' => false]);

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/overview')
            ->assertOk()
            ->assertJsonPath('meta.rsvps.attending', 1)
            ->assertJsonPath('meta.rsvps.declined', 1)
            // One guest plus their two companions.
            ->assertJsonPath('meta.rsvps.seats', 3);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/rsvps')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
