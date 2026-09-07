<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Je songtekst</title>
</head>
<body style="margin:0;padding:0;background:#f8faf6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;color:#0d1512;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center" style="padding:40px 20px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border-radius:16px;"><tr><td style="padding:32px;">
            @include('emails.partials.logo')
            <h1 style="font-size:26px;line-height:1.25;margin:0 0 14px;text-align:center;">Je songtekst is klaar</h1>
            <p style="font-size:15px;line-height:1.7;color:#4a5a52;margin:0 0 20px;">Hier is de definitieve tekst voor <strong>{{ $recipientName }}</strong> (aanvraag #{{ $orderId }}).</p>
            <pre style="white-space:pre-wrap;font-family:inherit;font-size:15px;line-height:1.7;background:#f3f7f3;border-radius:12px;padding:20px;margin:0;">{{ $lyrics }}</pre>
            @if($musicPreferences !== [])
                <h2 style="font-size:20px;line-height:1.3;margin:28px 0 12px;">Jouw muzikale voorkeuren</h2>
                <table width="100%" cellspacing="0" cellpadding="0" style="font-size:15px;line-height:1.7;">
                    @foreach($musicPreferences as $label => $value)
                        <tr>
                            <th scope="row" align="left" valign="top" style="padding:8px 16px 8px 0;color:#4a5a52;font-weight:600;">{{ $label }}</th>
                            <td valign="top" style="padding:8px 0;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </td></tr></table>
    </td></tr></table>
</body>
</html>
