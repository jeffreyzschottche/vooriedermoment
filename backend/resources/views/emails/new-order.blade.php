<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe betaalde aanvraag</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #0d1512; background: #f8faf6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 32px 20px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #5b6660; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td { padding: 6px 0; vertical-align: top; font-size: 14px; }
        td.k { color: #5b6660; width: 140px; }
        .panel { background: #fff; border: 1px solid #e3e8e1; border-radius: 10px; padding: 16px; margin-top: 8px; font-size: 14px; }
        pre { white-space: pre-wrap; font-family: inherit; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nieuwe betaalde aanvraag #{{ $order['order_id'] }}</h1>
        <p class="muted">{{ $order['category_title'] }} — €{{ $order['price_eur'] }}</p>

        <table>
            <tr><td class="k">Voor</td><td>{{ $order['recipient_name'] }}</td></tr>
            <tr><td class="k">E-mail klant</td><td>{{ $order['customer_email'] ?? '—' }}</td></tr>
            <tr><td class="k">Datum</td><td>{{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('d-m-Y H:i') }}</td></tr>
            <tr><td class="k">Suno-titel</td><td>{{ $order['suno']['title'] }}</td></tr>
            <tr><td class="k">Suno-stijl</td><td>{{ $order['suno']['style'] }}</td></tr>
        </table>

        <strong>Lyrics</strong>
        <div class="panel"><pre>{{ $order['suno']['lyrics'] }}</pre></div>

        <p class="muted" style="margin-top:24px;">
            De volledige Suno-JSON zit als bijlage bij deze mail en staat lokaal in je orders-map.
        </p>
    </div>
</body>
</html>
