<?php

namespace Tests\Feature;

use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_rsvp_endpoint_is_closed_without_recording_or_notifying(): void
    {
        Mail::fake();

        $this->postJson('/api/rsvp', [
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
        ])
            ->assertStatus(410)
            ->assertJsonPath('message', 'RSVP submissions are closed. Please use the pre-order or contact pages for current enquiries.');

        $this->assertDatabaseCount('rsvps', 0);
        Mail::assertNothingSent();
    }

    public function test_rsvps_are_listed_in_the_admin_area_with_attendance_counts(): void
    {
        Rsvp::create(['name' => 'Yes One', 'email' => 'y1@example.com', 'attending' => true, 'guests' => 2]);
        Rsvp::create(['name' => 'No One', 'email' => 'n1@example.com', 'attending' => false]);

        $admin = User::factory()->create(['role' => 'admin']);

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
