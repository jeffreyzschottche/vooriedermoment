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
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="padding: 40px;">
                            @include('emails.partials.logo')
                            <div style="text-align: center; margin-bottom: 32px;">
                                <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
                                <h1 style="font-size: 24px; color: #0d1512; margin: 0;">Klant heeft gekozen!</h1>
                            </div>

                            <div style="background: #f0fdf4; padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7;">
                                            <span style="color: #5b6660; font-size: 13px;">Order ID:</span>
                                        </td>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7; text-align: right;">
                                            <strong style="color: #0d1512;">#{{ $orderId }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7;">
                                            <span style="color: #5b6660; font-size: 13px;">Ontvanger:</span>
                                        </td>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7; text-align: right;">
                                            <strong style="color: #0d1512;">{{ $recipientName }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7;">
                                            <span style="color: #5b6660; font-size: 13px;">Categorie:</span>
                                        </td>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #dcfce7; text-align: right;">
                                            <strong style="color: #0d1512;">{{ $categoryTitle }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0;">
                                            <span style="color: #5b6660; font-size: 13px;">Klant e-mail:</span>
                                        </td>
                                        <td style="padding: 8px 0; text-align: right;">
                                            <a href="mailto:{{ $customerEmail }}" style="color: #16a34a;">{{ $customerEmail }}</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background: #16a34a; color: #ffffff; padding: 20px; border-radius: 12px; margin-bottom: 24px; text-align: center;">
                                <p style="margin: 0 0 4px; font-size: 13px; opacity: 0.8;">Gekozen sample (positie {{ $chosenPosition }}):</p>
                                <p style="margin: 0; font-size: 20px; font-weight: 700;">{{ $chosenTitle }}</p>
                            </div>

                            @if($sunoUrl)
                            <div style="margin-bottom: 24px; text-align: center;">
                                <a href="{{ $sunoUrl }}" style="display: inline-block; background: #0d1512; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 14px;">
                                    Open in Suno
                                </a>
                            </div>
                            @endif

                            <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                                <h3 style="margin: 0 0 12px; font-size: 14px; color: #0d1512;">Muziekstijl & Vocals</h3>
                                <p style="margin: 0 0 8px; color: #5b6660; font-size: 13px;"><strong>Stijl:</strong> {{ $musicStyle }}</p>
                                <p style="margin: 0; color: #5b6660; font-size: 13px;"><strong>Vocals:</strong> {{ $vocals }}</p>
                            </div>

                            @if($lyrics)
                            <div style="background: #f8fafc; padding: 20px; border-radius: 12px;">
                                <h3 style="margin: 0 0 12px; font-size: 14px; color: #0d1512;">Lyrics</h3>
                                <pre style="margin: 0; font-family: inherit; font-size: 13px; color: #5b6660; white-space: pre-wrap; line-height: 1.6;">{{ $lyrics }}</pre>
                            </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
