<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PickupPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreOrderResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function order(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'reference' => 'ENT-TEST123456',
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'quantity' => 2,
            'unit_amount' => 1500000,
            'total_amount' => 3000000,
            'status' => Order::STATUS_PAID,
        ], $overrides));
    }

    public function test_pickup_points_expose_the_fields_the_admin_form_edits(): void
    {
        PickupPoint::create(['name' => 'Primary collection point', 'address' => 'To be confirmed', 'is_active' => true, 'order' => 3]);

        $row = $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/admin/pickup-points')
            ->assertOk()
            ->json('data.0');

        // Omitting these let the edit form round-trip blanks back, silently
        // deactivating the point and resetting its position.
        $this->assertArrayHasKey('is_active', $row);
        $this->assertArrayHasKey('order', $row);
        $this->assertTrue($row['is_active']);
        $this->assertSame(3, $row['order']);
    }

    public function test_editing_one_pickup_point_field_preserves_the_others(): void
    {
        $point = PickupPoint::create(['name' => 'Primary collection point', 'address' => 'To be confirmed', 'is_active' => true, 'order' => 3]);
        $admin = $this->admin();

        $payload = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/pickup-points')->json('data.0');
        $payload['contact_phone'] = '0801 234 5678';
        unset($payload['id']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/pickup-points/{$point->id}", $payload)
            ->assertOk();

        $point->refresh();
        $this->assertTrue($point->is_active);
        $this->assertSame(3, $point->order);
        $this->assertSame('0801 234 5678', $point->contact_phone);
    }

    public function test_orders_expose_an_id_so_the_admin_can_target_a_row(): void
    {
        $pickupPoint = PickupPoint::create(['name' => 'Primary collection point', 'address' => 'To be confirmed', 'is_active' => true, 'order' => 3]);
        $order = $this->order(['pickup_point_id' => $pickupPoint->id, 'paid_at' => now()]);

        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/admin/orders')
            ->assertOk()
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.reference', 'ENT-TEST123456')
            ->assertJsonPath('data.0.pickup_point_name', 'Primary collection point')
            ->assertJsonPath('data.0.pickup_point_address', 'To be confirmed');
    }

    public function test_orders_cannot_be_created_edited_or_deleted_in_the_admin(): void
    {
        $order = $this->order();
        $admin = $this->admin();

        // An order is produced by the payment flow; hand-editing would desync it
        // from the provider, so the API refuses rather than silently no-opping.
        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/orders', ['name' => 'Hand Made'])
            ->assertForbidden();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/orders/{$order->id}", ['name' => 'Renamed'])
            ->assertForbidden();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/orders/{$order->id}")
            ->assertForbidden();

        $this->assertSame('Ada Builder', $order->fresh()->name);
    }

    public function test_pickup_points_remain_fully_editable(): void
    {
        $admin = $this->admin();

        $created = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/pickup-points', [
                'name' => 'Lekki collection point',
                'address' => '12 Admiralty Way',
                'city' => 'Lagos',
                'is_active' => true,
                'order' => 2,
            ])
            // Admin writes return 200 across every resource, not 201.
            ->assertSuccessful()
            ->json('data.id');

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/pickup-points/{$created}")
            ->assertOk();

        $this->assertDatabaseMissing('pickup_points', ['id' => $created]);
    }

    public function test_orders_can_be_exported(): void
    {
        $this->order();

        $response = $this->actingAs($this->admin(), 'sanctum')
            ->get('/api/admin/orders/export')
            ->assertOk()
            ->assertHeader('x-content-type-options', 'nosniff');

        $this->assertStringContainsString('ENT-TEST123456', $response->getContent());
        $this->assertStringContainsString('Pre-orders', $response->getContent());
    }
}
