<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f0fdf4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="min-height: 100vh;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 500px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="padding: 40px; text-align: center;">
                            @include('emails.partials.logo')
                            <div style="font-size: 48px; margin-bottom: 16px;">🎵</div>

                            <h1 style="font-size: 24px; color: #0d1512; margin: 0 0 16px;">Je samples zijn klaar!</h1>

                            <p style="color: #5b6660; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                Goed nieuws! We hebben 4 unieke versies gemaakt van jouw persoonlijke nummer voor <strong>{{ $recipientName }}</strong> ({{ $categoryTitle }}).
                            </p>

                            <div style="background: #f0fdf4; padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                                <p style="color: #5b6660; font-size: 13px; margin: 0 0 8px;">Je wachtwoord om de samples te beluisteren:</p>
                                <div style="font-size: 32px; font-weight: 700; color: #16a34a; letter-spacing: 4px; font-family: monospace;">{{ $password }}</div>
                            </div>

                            <a href="{{ $samplesUrl }}" style="display: inline-block; background: #16a34a; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 10px; font-weight: 600; font-size: 16px; margin-bottom: 24px;">
                                Beluister je samples
                            </a>

                            <div style="background: #fefce8; border: 1px solid #fef08a; padding: 16px; border-radius: 10px; text-align: left; margin-top: 24px;">
                                <p style="color: #854d0e; font-size: 13px; margin: 0;">
                                    <strong>Let op:</strong> Deze link is geldig tot {{ $expiresAt }}. Kies voor die tijd je favoriete versie, zodat wij je nummer kunnen afmaken en publiceren.
                                </p>
                            </div>

                            <p style="color: #5b6660; font-size: 13px; margin-top: 32px;">
                                Vragen? Mail ons op<br>
                                <a href="mailto:info@vooriedermoment.nl" style="color: #16a34a;">info@vooriedermoment.nl</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
