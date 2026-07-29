<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bedankt! - Voor Ieder Moment</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); color: #0d1512; margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 450px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        .logo { font-size: 20px; font-weight: 700; color: #16a34a; margin-bottom: 24px; }
        h1 { font-size: 24px; margin: 0 0 12px; }
        p { color: #5b6660; font-size: 15px; line-height: 1.6; }
        .chosen-info { background: #f0fdf4; padding: 20px; border-radius: 12px; margin: 24px 0; text-align: left; }
        .chosen-label { font-size: 12px; color: #5b6660; margin-bottom: 4px; }
        .chosen-title { font-weight: 600; font-size: 18px; }
        .next-steps { background: #fefce8; border: 1px solid #fef08a; padding: 16px; border-radius: 10px; text-align: left; margin-top: 24px; }
        .next-steps h3 { margin: 0 0 8px; font-size: 14px; color: #854d0e; }
        .next-steps p { color: #854d0e; font-size: 13px; margin: 0; }
        a { color: #16a34a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🎉</div>
        <div class="logo">Voor Ieder Moment</div>
        <h1>Bedankt voor je keuze!</h1>
        <p>Je hebt gekozen voor:</p>

        <div class="chosen-info">
            <div class="chosen-label">Jouw nummer</div>
            <div class="chosen-title">{{ $chosenSample->title }}</div>
        </div>

        <div class="next-steps">
            <h3>Wat gebeurt er nu?</h3>
            <p>Wij maken je nummer helemaal af en publiceren het op Spotify en andere streamingdiensten. Je ontvangt een e-mail zodra je nummer live staat!</p>
        </div>

        <p style="margin-top:24px;">
            Vragen? Mail ons op<br>
            <a href="mailto:info@vooriedermoment.nl">info@vooriedermoment.nl</a>
        </p>
    </div>
</body>
</html>
