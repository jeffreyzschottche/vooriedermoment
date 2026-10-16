<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productie mislukt</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8faf6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; color: #0d1512;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(13,21,18,0.08);">
                    <tr>
                        <td style="padding: 32px;">
                            @include('emails.partials.logo')

                            <h1 style="font-size: 24px; line-height: 1.25; margin: 0 0 12px;">Productie mislukt — aanvraag #{{ $orderId }}</h1>
                            <p style="font-size: 15px; line-height: 1.7; color: #4a5a52; margin: 0 0 20px;">
                                Deze betaalde order is na alle automatische pogingen vastgelopen. De klant heeft de mail
                                <em>"We controleren je persoonlijke nummer"</em> gekregen en wacht op jou.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f3f7f3; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px;">
                                <tr><td style="padding: 6px 0; color: #6a756f; font-size: 14px;">Moment</td><td align="right" style="padding: 6px 0; font-size: 14px;"><strong>{{ $categoryTitle }}</strong></td></tr>
                                <tr><td style="padding: 6px 0; color: #6a756f; font-size: 14px;">Voor</td><td align="right" style="padding: 6px 0; font-size: 14px;"><strong>{{ $recipientName }}</strong></td></tr>
                                <tr><td style="padding: 6px 0; color: #6a756f; font-size: 14px;">E-mail klant</td><td align="right" style="padding: 6px 0; font-size: 14px;"><strong>{{ $customerEmail ?? '—' }}</strong></td></tr>
                                @if($paidAt)
                                <tr><td style="padding: 6px 0; color: #6a756f; font-size: 14px;">Betaald op</td><td align="right" style="padding: 6px 0; font-size: 14px;"><strong>{{ $paidAt }}</strong></td></tr>
                                @endif
                            </table>

                            <p style="font-size: 14px; font-weight: 600; margin: 0 0 8px;">Foutmelding</p>
                            <pre style="white-space: pre-wrap; font-size: 13px; line-height: 1.6; background: #fff4ef; border: 1px solid #ffd8cc; border-radius: 12px; padding: 14px; margin: 0;">{{ $error }}</pre>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
