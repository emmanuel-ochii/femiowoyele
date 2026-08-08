<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrderRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PickupPointResource;
use App\Mail\OrderPlaced;
use App\Mail\OrderReceipt;
use App\Models\Book;
use App\Models\ContentBlock;
use App\Models\Order;
use App\Models\PickupPoint;
use App\Payments\PaymentGateway;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PreOrderController extends Controller
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /** Everything the pre-order page needs to render. */
    public function show(): JsonResponse
    {
        $slug = ContentBlock::where('slug', 'home.launch')->value('meta')['book_slug'] ?? 'entrusted';
        $unit = (int) config('payments.book_price_minor');

        return response()->json([
            'data' => [
                'book' => new BookResource(Book::where('slug', $slug)->first()),
                'pricing' => [
                    'unit_amount' => $unit,
                    'unit_display' => config('payments.currency_symbol', '₦').number_format($unit / 100, 2),
                    'currency' => config('payments.currency', 'NGN'),
                    'max_quantity' => (int) config('payments.max_quantity', 20),
                ],
                'pickup_points' => PickupPointResource::collection(PickupPoint::active()->get()),
                'payment' => [
                    'provider' => $this->gateway->name(),
                    // Lets the UI show a test-mode banner without guessing.
                    'is_test_mode' => $this->gateway->name() === 'mock',
                ],
            ],
        ]);
    }

    /** Creates a pending order and hands back where to send the buyer to pay. */
    public function store(PreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $unit = (int) config('payments.book_price_minor');
        $quantity = (int) $data['quantity'];

        $slug = ContentBlock::where('slug', 'home.launch')->value('meta')['book_slug'] ?? 'entrusted';

        $order = Order::create([
            'reference' => 'ENT-'.strtoupper(Str::random(10)),
            'book_id' => Book::where('slug', $slug)->value('id'),
            'pickup_point_id' => $data['pickup_point_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'quantity' => $quantity,
            // Priced server-side. The client never supplies an amount.
            'unit_amount' => $unit,
            'total_amount' => $unit * $quantity,
            'currency' => config('payments.currency', 'NGN'),
            'status' => Order::STATUS_PENDING,
            'payment_provider' => $this->gateway->name(),
        ]);

        $callback = rtrim((string) config('app.frontend_url', config('app.url')), '/')
            .config('payments.callback_path');

        $payment = $this->gateway->initialize($order, $callback);

        $order->update(['payment_reference' => $payment['reference']]);

        return response()->json([
            'data' => new OrderResource($order),
            'meta' => ['authorization_url' => $payment['authorization_url']],
        ], 201);
    }

    /**
     * Confirms a payment with the provider and records the outcome.
     *
     * Status is always taken from the gateway, never from the client, so a
     * crafted request cannot mark an order paid.
     */
    public function verify(string $reference): JsonResponse
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        if ($order->isPaid()) {
            return $this->orderResponse($order);
        }

        $result = $this->gateway->verify($reference);

        $order->update([
            'status' => $result->successful ? Order::STATUS_PAID : Order::STATUS_FAILED,
            'paid_at' => $result->successful ? now() : null,
            'payment_meta' => $result->meta,
        ]);

        if ($result->successful) {
            Notifier::send(new OrderPlaced($order));
            Notifier::sendTo($order->email, new OrderReceipt($order));
        }

        return $this->orderResponse($order, $result->message);
    }

    /** The order plus, once paid, where the book can be collected. */
    public function status(string $reference): JsonResponse
    {
        return $this->orderResponse(Order::where('reference', $reference)->firstOrFail());
    }

    /** Test-mode only: lets the simulated checkout record a cancellation. */
    public function simulate(Request $request, string $reference): JsonResponse
    {
        abort_unless($this->gateway->name() === 'mock', 404);

        $order = Order::where('reference', $reference)->firstOrFail();

        if (! $order->isPaid() && $request->boolean('cancel')) {
            $order->update(['status' => Order::STATUS_FAILED]);
        }

        return $this->orderResponse($order);
    }

    private function orderResponse(Order $order, ?string $message = null): JsonResponse
    {
        $order->load(['book', 'pickupPoint']);

        return response()->json([
            'data' => new OrderResource($order),
            'meta' => [
                'message' => $message,
                // Collection details are only meaningful once payment succeeded.
                'pickup_points' => $order->isPaid()
                    ? PickupPointResource::collection(PickupPoint::active()->get())
                    : [],
            ],
        ]);
    }
}
