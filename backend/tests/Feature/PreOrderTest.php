<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Mail\OrderReceipt;
use App\Models\Order;
use App\Models\PickupPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PreOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['payments.book_price_minor' => 1500000, 'payments.driver' => 'mock']);

        PickupPoint::create(['name' => 'Launch evening', 'address' => '5 Alade Avenue', 'city' => 'Lagos', 'order' => 1]);
        PickupPoint::create(['name' => 'Retired point', 'address' => 'Closed', 'is_active' => false, 'order' => 2]);
    }

    private function placeOrder(array $overrides = []): string
    {
        $response = $this->postJson('/api/pre-order', array_merge([
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'quantity' => 2,
        ], $overrides))->assertCreated();

        return $response->json('data.reference');
    }

    public function test_it_prices_the_order_on_the_server(): void
    {
        $reference = $this->placeOrder();

        $this->assertDatabaseHas('orders', [
            'reference' => $reference,
            'unit_amount' => 1500000,
            'total_amount' => 3000000,
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function test_a_client_supplied_amount_is_ignored(): void
    {
        // A crafted request must not be able to set its own price.
        $reference = $this->placeOrder(['unit_amount' => 1, 'total_amount' => 1]);

        $this->assertSame(3000000, Order::where('reference', $reference)->value('total_amount'));
    }

    public function test_it_returns_somewhere_to_send_the_buyer(): void
    {
        $response = $this->postJson('/api/pre-order', ['name' => 'Ada', 'email' => 'ada@example.com', 'quantity' => 1])
            ->assertCreated()
            ->assertJsonPath('data.status', Order::STATUS_PENDING)
            ->assertJsonStructure(['meta' => ['authorization_url']]);

        $this->assertArrayNotHasKey('id', $response->json('data'));
    }

    public function test_pickup_points_are_withheld_until_payment_succeeds(): void
    {
        $reference = $this->placeOrder();

        $this->getJson("/api/pre-order/{$reference}")
            ->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_PENDING)
            ->assertJsonCount(0, 'meta.pickup_points');
    }

    public function test_verifying_marks_the_order_paid_and_releases_pickup_points(): void
    {
        Mail::fake();
        $reference = $this->placeOrder();

        $this->postJson("/api/pre-order/{$reference}/verify")
            ->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_PAID)
            // Only the active point is offered.
            ->assertJsonCount(1, 'meta.pickup_points')
            ->assertJsonPath('meta.pickup_points.0.name', 'Launch evening');

        $this->assertNotNull(Order::where('reference', $reference)->value('paid_at'));
    }

    public function test_a_cancelled_payment_stays_unpaid_and_reveals_nothing(): void
    {
        $reference = $this->placeOrder();

        $this->postJson("/api/pre-order/{$reference}/simulate", ['cancel' => true])->assertOk();

        $this->postJson("/api/pre-order/{$reference}/verify")
            ->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_FAILED)
            ->assertJsonCount(0, 'meta.pickup_points');
    }

    public function test_verifying_twice_does_not_duplicate_the_confirmation(): void
    {
        Mail::fake();
        $reference = $this->placeOrder();

        $this->postJson("/api/pre-order/{$reference}/verify")->assertOk();
        $this->postJson("/api/pre-order/{$reference}/verify")->assertOk();

        // A refresh of the callback URL must not send a second receipt.
        Mail::assertSent(OrderReceipt::class, 1);
        Mail::assertSent(OrderPlaced::class, 1);
    }

    public function test_a_paid_order_emails_both_the_buyer_and_the_team(): void
    {
        Mail::fake();
        config([
            'mail.notifications.to' => 'faithdolapo27@gmail.com',
            'mail.notifications.cc' => ['profemative@gmail.com'],
        ]);

        $reference = $this->placeOrder();
        $this->postJson("/api/pre-order/{$reference}/verify")->assertOk();

        Mail::assertSent(OrderReceipt::class, fn (OrderReceipt $mail) => $mail->hasTo('ada@example.com'));
        Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail) => $mail->hasTo('faithdolapo27@gmail.com')
            && $mail->hasCc('profemative@gmail.com'));
    }

    public function test_it_validates_the_order(): void
    {
        $this->postJson('/api/pre-order', ['name' => 'A', 'email' => 'nope', 'quantity' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'quantity']);
    }

    public function test_it_rejects_an_unreasonable_quantity(): void
    {
        $this->postJson('/api/pre-order', ['name' => 'Ada', 'email' => 'ada@example.com', 'quantity' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    }

    public function test_an_unknown_reference_is_not_found(): void
    {
        $this->getJson('/api/pre-order/ENT-DOESNOTEXIST')->assertNotFound();
    }

    public function test_the_receipt_lists_the_pickup_points(): void
    {
        $reference = $this->placeOrder();
        $this->postJson("/api/pre-order/{$reference}/verify")->assertOk();

        $html = (new OrderReceipt(Order::where('reference', $reference)->first()))->render();

        $this->assertStringContainsString('Launch evening', $html);
        $this->assertStringContainsString($reference, $html);
        $this->assertStringNotContainsString('Retired point', $html);
    }
}
