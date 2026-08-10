<?php

namespace App\Support;

use App\Mail\OrderPlaced;
use App\Mail\OrderReceipt;
use App\Models\Order;
use App\Payments\PaymentResult;

class OrderPaymentRecorder
{
    public function record(Order $order, PaymentResult $result): Order
    {
        if ($order->isPaid()) {
            return $order->refresh();
        }

        $status = $result->orderStatus ?? ($result->successful ? Order::STATUS_PAID : Order::STATUS_FAILED);
        $meta = array_replace_recursive((array) $order->payment_meta, $result->meta);

        $order->update([
            'status' => $status,
            'paid_at' => $result->successful ? now() : $order->paid_at,
            'payment_meta' => $meta,
        ]);

        if ($result->successful) {
            Notifier::send(new OrderPlaced($order));
            Notifier::sendTo($order->email, new OrderReceipt($order));
        }

        return $order->refresh();
    }
}
