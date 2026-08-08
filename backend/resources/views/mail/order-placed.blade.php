@extends('mail.layout')

@section('title', 'Pre-order — ' . $order->name)
@section('eyebrow', 'Entrusted · Pre-order')
@section('preview', $order->name . ' ordered ' . $order->quantity . ' copy(ies)')

@section('content')
    <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:24px; line-height:32px; color:#0b1c32;">
        {{ $order->name }} pre-ordered {{ $order->quantity }} {{ Str::plural('copy', $order->quantity) }}.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        @foreach ([
            'Reference' => $order->reference,
            'Name' => $order->name,
            'Email' => $order->email,
            'Phone' => $order->phone ?: '—',
            'Quantity' => (string) $order->quantity,
            'Total paid' => $order->formattedTotal(),
            'Pickup point' => $order->pickupPoint?->name ?: 'Not selected',
        ] as $label => $value)
            <tr>
                <td style="padding:10px 0; border-bottom:1px solid rgba(11,28,50,0.08); width:150px; vertical-align:top;
                           font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold; letter-spacing:1.2px;
                           text-transform:uppercase; color:#6f7a8b;">{{ $label }}</td>
                <td style="padding:10px 0; border-bottom:1px solid rgba(11,28,50,0.08); vertical-align:top;
                           font-family:Helvetica,Arial,sans-serif; font-size:15px; line-height:22px; color:#182235;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin:26px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:13px; line-height:22px; color:#6f7a8b;">
        Paid {{ $order->paid_at?->format('j F Y \a\t g:ia') }} via {{ $order->payment_provider }}.
        Full list in the admin studio under Pre-orders.
    </p>
@endsection
