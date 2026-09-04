<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We controleren je aanvraag</title>
</head>
<body style="margin:0;padding:0;background:#f8faf6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;color:#0d1512;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center" style="padding:40px 20px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#fff;border-radius:16px;"><tr><td style="padding:32px;">
            @include('emails.partials.logo')
            <h1 style="font-size:26px;line-height:1.25;margin:0 0 14px;text-align:center;">We controleren je nummer even persoonlijk</h1>
            <p style="font-size:15px;line-height:1.7;color:#4a5a52;margin:0 0 18px;">Je betaling en aanvraag voor <strong>{{ $recipientName }}</strong> zijn goed ontvangen. Bij de automatische controle van de persoonlijke songtekst is extra aandacht nodig, zodat geen van je ingevulde details verloren gaat.</p>
            <p style="font-size:15px;line-height:1.7;color:#4a5a52;margin:0 0 18px;">Je hoeft niets te doen en betaalt niets extra. We pakken aanvraag <strong>#{{ $orderId }}</strong> handmatig op en sturen je de samples zodra alles klopt.</p>
            <p style="font-size:13px;line-height:1.6;color:#7a8f82;margin:26px 0 0;text-align:center;">Vragen? Mail naar <a href="mailto:info@vooriedermoment.nl" style="color:#e04a2a;">info@vooriedermoment.nl</a></p>
        </td></tr></table>
    </td></tr></table>
</body>
</html>
