@extends('mail.layout')

@section('title', 'Your pre-order of Entrusted')
@section('eyebrow', 'Entrusted · Pre-order confirmed')
@section('preview', 'Your pre-order is confirmed. Reference ' . $order->reference)
@section('footer', 'You are receiving this because you pre-ordered Entrusted. Replies reach the team.')

@section('content')
    <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:24px; line-height:32px; color:#0b1c32;">
        Thank you, {{ $order->name }}.
    </p>

    <p style="margin:20px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:16px; line-height:27px; color:#182235;">
        Your pre-order of <em>Entrusted</em> is confirmed. Please quote reference
        <strong>{{ $order->reference }}</strong> when collecting your {{ Str::plural('copy', $order->quantity) }}.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        @foreach ([
            'Reference' => $order->reference,
            'Quantity' => (string) $order->quantity,
            'Total paid' => $order->formattedTotal(),
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

    @if ($pickupPoints->isNotEmpty())
        <p style="margin:30px 0 12px; font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold;
                  letter-spacing:1.2px; text-transform:uppercase; color:#6f7a8b;">Where to collect</p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="border:1px solid rgba(11,28,50,0.10);">
            @foreach ($pickupPoints as $point)
                <tr>
                    <td style="padding:16px 20px; {{ $loop->last ? '' : 'border-bottom:1px solid rgba(11,28,50,0.08);' }}
                               font-family:Helvetica,Arial,sans-serif; font-size:15px; line-height:23px; color:#182235;">
                        <strong style="color:#0b1c32;">{{ $point->name }}</strong><br />
                        <span style="color:#4b5768;">{{ $point->address }}@if ($point->city), {{ $point->city }}@endif</span>
                        @if ($point->opening_hours)
                            <br /><span style="color:#6f7a8b; font-size:13px;">{{ $point->opening_hours }}</span>
                        @endif
                        @if ($point->contact_phone)
                            <br /><span style="color:#6f7a8b; font-size:13px;">{{ $point->contact_phone }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <p style="margin:30px 0 0; font-family:Georgia,'Times New Roman',serif; font-size:17px; line-height:26px; color:#0b1c32;">
        Warm regards,<br />Femi Owoyele
    </p>
@endsection
