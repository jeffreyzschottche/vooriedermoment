<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al gekozen - Voor Ieder Moment</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); color: #0d1512; margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 400px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center; }
        .icon { font-size: 48px; margin-bottom: 16px; }
        .logo { font-size: 20px; font-weight: 700; color: #16a34a; margin-bottom: 24px; }
        h1 { font-size: 20px; margin: 0 0 12px; }
        p { color: #5b6660; font-size: 14px; line-height: 1.6; }
        .chosen-title { background: #f0fdf4; padding: 16px; border-radius: 10px; font-weight: 600; margin: 20px 0; }
        a { color: #16a34a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <div class="logo">Voor Ieder Moment</div>
        <h1>Je hebt al gekozen!</h1>
        <div class="chosen-title">{{ $songRequest->chosen_sample_title }}</div>
        <p>Je nummer wordt nu afgerond en gepubliceerd. We sturen je een e-mail zodra het klaar is!</p>
        <p style="margin-top:24px;">
            Vragen? Mail ons op<br>
            <a href="mailto:info@vooriedermoment.nl">info@vooriedermoment.nl</a>
        </p>
    </div>
</body>
</html>
