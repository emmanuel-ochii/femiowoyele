@extends('mail.layout')

@section('title', 'Thank You for Your RSVP')
@section('eyebrow', 'Celebrating Forty Years · RSVP')
@section('preview', 'Thank you for your RSVP.')
@section('footer', 'You are receiving this because you responded to an invitation from Femi Owoyele. Replies reach the event team.')

@section('content')
    <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:24px; line-height:32px; color:#0b1c32;">
        Dear {{ $rsvp->name }},
    </p>

    <p style="margin:20px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:16px; line-height:27px; color:#182235;">
        {{ $body }}
    </p>

    @if ($rsvp->attending)
        @if ($details)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="margin-top:30px; background-color:#fbf9f5; border-left:2px solid #c9a45c;">
            <tr>
                <td style="padding:20px 24px;">
                    <p style="margin:0 0 14px; font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold;
                              letter-spacing:1.2px; text-transform:uppercase; color:#6f7a8b;">
                        The evening
                    </p>
                    @foreach ($details as $label => $value)
                        <p style="margin:0 0 6px; font-family:Helvetica,Arial,sans-serif; font-size:15px; line-height:24px; color:#182235;">
                            <span style="color:#6f7a8b;">{{ $label }}:</span> {{ $value }}
                        </p>
                    @endforeach
                </td>
            </tr>
        </table>
        @endif

        <p style="margin:26px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:24px; color:#8c6a31;">
            Please note this is an adults-only event.
        </p>
    @endif

    <p style="margin:30px 0 0; font-family:Georgia,'Times New Roman',serif; font-size:17px; line-height:26px; color:#0b1c32;">
        Warm regards,<br />
        Femi Owoyele
    </p>
@endsection
