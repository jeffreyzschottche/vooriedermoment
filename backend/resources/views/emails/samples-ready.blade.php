<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Je samples zijn klaar!</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #0d1512;
            background-color: #f8faf6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(13, 21, 18, 0.08);
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo img {
            height: 40px;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #0d1512;
            margin: 0 0 16px;
        }
        p {
            color: #4a5a52;
            margin: 0 0 16px;
        }
        .samples {
            margin: 32px 0;
        }
        .sample {
            background: #f3f7f3;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .sample-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .sample-number {
            font-size: 14px;
            font-weight: 700;
            color: #e04a2a;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .sample-title {
            font-size: 18px;
            font-weight: 600;
            color: #0d1512;
        }
        .listen-btn {
            display: inline-block;
            background: linear-gradient(135deg, #ff6a4a 0%, #e04a2a 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .cta {
            text-align: center;
            margin-top: 32px;
            padding: 24px;
            background: linear-gradient(135deg, #0d1512 0%, #1a3530 100%);
            border-radius: 12px;
        }
        .cta h2 {
            color: white;
            font-size: 20px;
            margin: 0 0 8px;
        }
        .cta p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin: 0 0 16px;
        }
        .cta a {
            color: #f5c042;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 32px;
            color: #7a8f82;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <img src="{{ config('app.url') }}/logozwart.png" alt="Voor Ieder Moment">
            </div>

            <h1>Hoi {{ $recipientName }}!</h1>

            <p>
                Goed nieuws! We hebben <strong>4 unieke samples</strong> gemaakt voor je
                <strong>{{ $songRequest->category_title }}</strong> nummer.
            </p>

            <p>
                Luister ze hieronder en reply op deze mail met het nummer van je favoriet.
                Wij maken dan de volledige versie en uploaden het naar Spotify!
            </p>

            <div class="samples">
                @foreach($samples as $index => $sample)
                <div class="sample">
                    <div class="sample-header">
                        <span class="sample-number">Sample {{ $index + 1 }}</span>
                    </div>
                    <div class="sample-title">{{ $sample['title'] }}</div>
                    <p style="font-size: 14px; margin: 8px 0 16px;">
                        Duur: {{ $sample['duration'] ?? 15 }} seconden
                    </p>
                    <a href="{{ $sample['url'] }}" class="listen-btn" target="_blank">
                        ▶ Luister sample {{ $index + 1 }}
                    </a>
                </div>
                @endforeach
            </div>

            <div class="cta">
                <h2>Je keuze doorgeven?</h2>
                <p>
                    Klik hieronder om je favoriet te kiezen:
                </p>
                <a href="{{ config('app.frontend_url') }}/kies/{{ $songRequest->selection_token }}" style="display: inline-block; margin-top: 16px; background: linear-gradient(135deg, #ff6a4a 0%, #e04a2a 100%); color: white; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: 600;">
                    Kies mijn favoriet
                </a>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Voor Ieder Moment</p>
                <p>Gepersonaliseerde nummers op Spotify</p>
            </div>
        </div>
    </div>
</body>
</html>
