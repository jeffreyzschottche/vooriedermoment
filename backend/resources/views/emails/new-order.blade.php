<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe bestelling</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #0d1512; background: #f8faf6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 32px 20px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 16px; margin: 24px 0 0; }
        .paid { display: inline-block; background: #dcfce7; color: #166534; font-weight: 600; font-size: 14px; padding: 6px 12px; border-radius: 999px; margin: 8px 0 0; }
        .muted { color: #5b6660; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 0; }
        td { padding: 6px 0; vertical-align: top; font-size: 14px; }
        td.k { color: #5b6660; width: 140px; }
        .panel { background: #fff; border: 1px solid #e3e8e1; border-radius: 10px; padding: 16px; margin-top: 8px; font-size: 14px; }
        .lyrics { white-space: pre-wrap; font-family: inherit; margin: 0; line-height: 1.8; }
        .btn { display: inline-block; background: #16a34a; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; margin: 16px 0; }
        .btn:hover { background: #15803d; }
    </style>
</head>
<body>
    <div class="container">
        @include('emails.partials.logo')
        <h1>Nieuwe bestelling #{{ $order['order_id'] }} — {{ $order['category_title'] }}</h1>
        <p class="paid">✓ Betaald — {{ $payment['Bedrag'] }} via {{ $payment['Betaalmethode'] }}</p>

        @if(!empty($order['admin_upload_url']))
        <div><a href="{{ $order['admin_upload_url'] }}" class="btn">4 Samples uploaden</a></div>
        @endif

        <h2>Betaling</h2>
        <table>
            @foreach($payment as $label => $value)
            <tr><td class="k">{{ $label }}</td><td>{{ $value }}</td></tr>
            @endforeach
        </table>

        <h2>Contactgegevens</h2>
        <table>
            @foreach($contact as $label => $value)
            <tr><td class="k">{{ $label }}</td><td>{{ $value }}</td></tr>
            @endforeach
        </table>

        @if($music !== [])
        <h2>Muziek</h2>
        <table>
            @foreach($music as $label => $value)
            <tr><td class="k">{{ $label }}</td><td>{{ $value }}</td></tr>
            @endforeach
            <tr><td class="k">Suno-titel</td><td>{{ $order['suno']['title'] }}</td></tr>
        </table>
        @endif

        <h2>Lyrics</h2>
        <div class="panel"><div class="lyrics">{{ str_replace(['\n', '\\n'], "\n", $order['suno']['lyrics']) }}</div></div>

        @if($answers !== [])
        <h2>Alle formulierantwoorden</h2>
        <table>
            @foreach($answers as $label => $value)
            <tr><td class="k">{{ $label }}</td><td>{{ $value }}</td></tr>
            @endforeach
        </table>
        @endif

        <p class="muted" style="margin-top:24px;">
            De klant heeft een bevestiging met deze songtekst ontvangen. De volledige Suno-JSON zit als bijlage bij deze mail.
            Dit is de enige mail die je voor deze bestelling krijgt.
        </p>
    </div>
</body>
</html>
