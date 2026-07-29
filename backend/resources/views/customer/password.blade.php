<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samples beluisteren - Voor Ieder Moment</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); color: #0d1512; margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 400px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center; }
        .logo { font-size: 28px; font-weight: 700; color: #16a34a; margin-bottom: 8px; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        .subtitle { color: #5b6660; font-size: 14px; margin-bottom: 32px; }
        label { display: block; text-align: left; font-weight: 500; margin-bottom: 8px; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 14px 16px; border: 2px solid #e3e8e1; border-radius: 10px; font-size: 18px; text-align: center; letter-spacing: 4px; font-weight: 600; }
        input[type="text"]:focus { outline: none; border-color: #16a34a; }
        .btn { display: block; width: 100%; background: #16a34a; color: #fff; padding: 14px; border: none; border-radius: 10px; font-weight: 600; font-size: 16px; cursor: pointer; margin-top: 20px; }
        .btn:hover { background: #15803d; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .hint { color: #5b6660; font-size: 13px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Voor Ieder Moment</div>
        <h1>Je samples zijn klaar!</h1>
        <p class="subtitle">Voer je wachtwoord in om de samples te beluisteren.</p>

        @if($errors->has('password'))
            <div class="error">{{ $errors->first('password') }}</div>
        @endif

        <form action="{{ route('samples.verify', ['token' => $token]) }}" method="POST">
            @csrf
            <label for="password">Wachtwoord (6 cijfers)</label>
            <input type="text" name="password" id="password" maxlength="6" pattern="\d{6}" inputmode="numeric" autocomplete="off" required autofocus>
            <button type="submit" class="btn">Samples bekijken</button>
        </form>

        <p class="hint">Je wachtwoord staat in de e-mail die je hebt ontvangen.</p>
    </div>
</body>
</html>
