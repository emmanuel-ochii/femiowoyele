{{-- Table layout with inline styles: the only markup email clients render
     consistently. Kept to the site palette so notifications look like the brand. --}}
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="color-scheme" content="light" />
    <title>@yield('title', 'Notification')</title>
  </head>
  <body style="margin:0; padding:0; background-color:#f4efe7; -webkit-font-smoothing:antialiased;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">@yield('preview')</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4efe7;">
      <tr>
        <td align="center" style="padding:32px 16px;">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                 style="max-width:600px; background-color:#ffffff; border:1px solid rgba(11,28,50,0.10);">

            <tr>
              <td style="background-color:#0b1c32; padding:26px 32px;">
                <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:19px; color:#ffffff;">
                  Femi Owoyele
                </p>
                <p style="margin:8px 0 0; font-family:Helvetica,Arial,sans-serif; font-size:11px; font-weight:bold;
                          letter-spacing:1.6px; text-transform:uppercase; color:#c9a45c;">
                  @yield('eyebrow', 'Website notification')
                </p>
              </td>
            </tr>

            <tr>
              <td style="padding:32px;">
                @yield('content')
              </td>
            </tr>

            <tr>
              <td style="border-top:1px solid rgba(11,28,50,0.10); padding:20px 32px;">
                <p style="margin:0; font-family:Helvetica,Arial,sans-serif; font-size:12px; line-height:20px; color:#6f7a8b;">
                  Sent automatically from femiowoyele.com. Reply to this email to respond directly to the sender.
                </p>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
