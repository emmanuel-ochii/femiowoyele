<?php

namespace Tests\Feature;

use App\Models\ContentBlock;
use App\Models\Rsvp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    private function setDeadline(?string $closesAt): void
    {
        ContentBlock::updateOrCreate(
            ['slug' => 'home.launch'],
            ['context' => 'home', 'title' => 'Entrusted', 'body' => '', 'order' => 4, 'meta' => [
                'book_slug' => 'entrusted',
                'rsvp_closes_at' => $closesAt,
            ]],
        );
    }

    public function test_rsvps_are_refused_once_the_deadline_has_passed(): void
    {
        $this->setDeadline('2026-08-11T23:59:59+01:00');
        $this->travelTo('2026-08-12 09:00:00');

        $this->postJson('/api/rsvp', ['name' => 'Late Guest', 'email' => 'late@example.com', 'attending' => true])
            ->assertStatus(422)
            ->assertJsonValidationErrors('attending');

        $this->assertDatabaseCount('rsvps', 0);
    }

    public function test_rsvps_are_accepted_up_to_the_end_of_the_deadline_day(): void
    {
        $this->setDeadline('2026-08-11T23:59:59+01:00');
        // 11 August is inclusive: responses are open all day.
        $this->travelTo('2026-08-11 22:30:00');

        $this->postJson('/api/rsvp', ['name' => 'Just In Time', 'email' => 'intime@example.com', 'attending' => true])
            ->assertCreated();
    }

    public function test_an_existing_guest_cannot_change_their_answer_after_the_deadline(): void
    {
        $this->setDeadline('2026-08-11T23:59:59+01:00');
        Rsvp::create(['name' => 'Ada', 'email' => 'ada@example.com', 'attending' => true]);

        $this->travelTo('2026-08-12 09:00:00');

        $this->postJson('/api/rsvp', ['name' => 'Ada', 'email' => 'ada@example.com', 'attending' => false])
            ->assertStatus(422);

        $this->assertTrue(Rsvp::where('email', 'ada@example.com')->value('attending'));
    }

    public function test_rsvps_stay_open_when_no_deadline_is_configured(): void
    {
        $this->setDeadline(null);
        $this->travelTo('2030-01-01 09:00:00');

        $this->postJson('/api/rsvp', ['name' => 'No Deadline', 'email' => 'none@example.com', 'attending' => true])
            ->assertCreated();
    }

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
            ->assertJsonPath('meta.updated', false);

        $this->assertDatabaseHas('rsvps', ['email' => 'ada@example.com', 'attending' => true, 'guests' => 0]);
    }

    public function test_browser_origin_rsvp_does_not_require_a_csrf_cookie(): void
    {
        Mail::fake();

        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
        ], [
            'Origin' => 'https://www.femiowoyele.com',
        ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'ada@example.com');
    }

    public function test_resubmitting_updates_the_existing_answer_rather_than_duplicating(): void
    {
        $payload = ['name' => 'Ada Builder', 'email' => 'ada@example.com', 'attending' => true, 'guests' => 2];

        $this->postJson('/api/rsvp', $payload)->assertCreated();

        $this->postJson('/api/rsvp', [...$payload, 'attending' => false])
            ->assertOk()
            ->assertJsonPath('data.attending', false)
            ->assertJsonPath('meta.updated', true);

        $this->assertSame(1, Rsvp::count());
        $this->assertDatabaseHas('rsvps', ['email' => 'ada@example.com', 'guests' => 0]);
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
            // This is a closed event: one attending RSVP is one seat.
            ->assertJsonPath('meta.rsvps.seats', 1);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/rsvps')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
