@extends('mail.layout')

@section('title', 'Enquiry — ' . $enquiry->subject)
@section('eyebrow', 'Website enquiry')
@section('preview', $enquiry->name . ' — ' . $enquiry->subject)

@section('content')
    <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:24px; line-height:32px; color:#0b1c32;">
        {{ $enquiry->subject }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        @foreach ([
            'From' => $enquiry->name,
            'Email' => $enquiry->email,
            'Nature' => ucfirst($enquiry->type),
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

    <p style="margin:28px 0 10px; font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold;
              letter-spacing:1.2px; text-transform:uppercase; color:#6f7a8b;">
        Message
    </p>
    {{-- nl2br over an escaped value: preserves the sender's line breaks without
         letting their input inject markup into the email. --}}
    <div style="padding:18px 20px; background-color:#fbf9f5; border-left:2px solid #c9a45c;
                font-family:Helvetica,Arial,sans-serif; font-size:15px; line-height:24px; color:#182235;">
        {!! nl2br(e($enquiry->message)) !!}
    </div>

    <p style="margin:26px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:13px; line-height:22px; color:#6f7a8b;">
        Received {{ $enquiry->created_at?->format('j F Y \a\t g:ia') }}.
    </p>
@endsection
