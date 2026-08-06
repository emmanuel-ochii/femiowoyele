@extends('mail.layout')

@section('title', 'RSVP — ' . $rsvp->name)
@section('eyebrow', 'Entrusted launch · RSVP')
@section('preview', $rsvp->name . ($rsvp->attending ? ' is attending' : ' cannot attend'))

@section('content')
    <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:24px; line-height:32px; color:#0b1c32;">
        @if ($wasUpdated)
            {{ $rsvp->name }} updated their RSVP.
        @elseif ($rsvp->attending)
            {{ $rsvp->name }} is attending.
        @else
            {{ $rsvp->name }} cannot make it.
        @endif
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        @foreach ([
            'Name' => $rsvp->name,
            'Email' => $rsvp->email,
            'Attending' => $rsvp->attending ? 'Yes' : 'No',
            'Note' => $rsvp->note ?: '—',
        ] as $label => $value)
            <tr>
                <td style="padding:10px 0; border-bottom:1px solid rgba(11,28,50,0.08); width:150px; vertical-align:top;
                           font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold; letter-spacing:1.2px;
                           text-transform:uppercase; color:#6f7a8b;">
                    {{ $label }}
                </td>
                <td style="padding:10px 0; border-bottom:1px solid rgba(11,28,50,0.08); vertical-align:top;
                           font-family:Helvetica,Arial,sans-serif; font-size:15px; line-height:22px; color:#182235;">
                    {{ $value }}
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin:26px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:13px; line-height:22px; color:#6f7a8b;">
        Recorded {{ $rsvp->updated_at?->format('j F Y \a\t g:ia') }}. The full RSVP list is in the admin studio under
        Launch RSVPs.
    </p>
@endsection
