<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Je bestelling is bevestigd</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8faf6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; color: #0d1512;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(13,21,18,0.08);">
                    <tr>
                        <td style="padding: 32px;">
                            @include('emails.partials.logo')

                            <h1 style="font-size: 26px; line-height: 1.25; margin: 0 0 14px; text-align: center;">Bedankt voor je bestelling!</h1>
                            <p style="font-size: 15px; line-height: 1.7; color: #4a5a52; margin: 0 0 24px; text-align: center;">
                                We hebben je aanvraag en betaling goed ontvangen. We gaan aan de slag met het persoonlijke nummer voor <strong>{{ $recipientName }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f3f7f3; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px;">
                                <tr><td style="padding: 7px 0; color: #6a756f; font-size: 14px;">Aanvraag</td><td align="right" style="padding: 7px 0; font-size: 14px;"><strong>#{{ $orderId }}</strong></td></tr>
                                <tr><td style="padding: 7px 0; color: #6a756f; font-size: 14px;">Moment</td><td align="right" style="padding: 7px 0; font-size: 14px;"><strong>{{ $categoryTitle }}</strong></td></tr>
                                <tr><td style="padding: 7px 0; color: #6a756f; font-size: 14px;">Bedrag</td><td align="right" style="padding: 7px 0; font-size: 14px;"><strong>€{{ $amount }}</strong></td></tr>
                                <tr><td style="padding: 7px 0; color: #6a756f; font-size: 14px;">Betaalmethode</td><td align="right" style="padding: 7px 0; font-size: 14px;"><strong>{{ $paymentMethod }}</strong></td></tr>
                                @if($paidAt)
                                <tr><td style="padding: 7px 0; color: #6a756f; font-size: 14px;">Ontvangen op</td><td align="right" style="padding: 7px 0; font-size: 14px;"><strong>{{ $paidAt }}</strong></td></tr>
                                @endif
                            </table>

                            <div style="background: #fff4ef; border: 1px solid #ffd8cc; border-radius: 12px; padding: 18px;">
                                <p style="font-size: 15px; line-height: 1.7; color: #4a5a52; margin: 0;">
                                    Binnen 24–72 uur ontvang je per e-mail vier samples van 15 seconden. Je kunt ze rustig beluisteren en daarna je favoriete versie kiezen.
                                </p>
                            </div>

                            <p style="font-size: 13px; line-height: 1.6; color: #7a8f82; margin: 26px 0 0; text-align: center;">
                                Vragen? Mail naar <a href="mailto:info@vooriedermoment.nl" style="color: #e04a2a;">info@vooriedermoment.nl</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
