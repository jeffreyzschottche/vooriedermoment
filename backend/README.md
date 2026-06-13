# Voor Ieder Moment Backend

Laravel 12 API voor aanvragen, concept-lyrics, checkout-status en auth.

## Status

De backend is technisch lokaal werkend en kan als API live gezet worden. De huidige betaalflow gebruikt nog `PAYMENT_PROVIDER=stub`: een checkout wordt direct als betaald gemarkeerd zonder echte Mollie/Stripe betaling. Ook de daadwerkelijke productie van het nummer is nog niet gekoppeld; na checkout staat daarvoor een TODO in `SongRequestController`.

## Lokale Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

De frontend verwacht lokaal:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api/v1
```

## Live Setup

Zet de backend op een PHP-host met PHP 8.2+, Composer en een database. De webroot moet naar `backend/public` wijzen.

Minimale productie-omgeving:

```env
APP_NAME="Voor Ieder Moment"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.vooriedermoment.nl
FRONTEND_URL=https://vooriedermoment.nl

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=info@vooriedermoment.nl
MAIL_FROM_NAME="Voor Ieder Moment"

saleOn=1
AI_PROVIDER=null
PAYMENT_PROVIDER=stub
MUSIC_PROVIDER=stub
```

Na deploy:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Als de frontend live staat, zet daar:

```env
NUXT_PUBLIC_API_BASE_URL=https://api.vooriedermoment.nl/api/v1
```

## API Endpoints

Alle endpoints staan onder `/api/v1`.

| Methode | Endpoint | Beschrijving |
| --- | --- | --- |
| POST | `/song-requests` | Maakt een aanvraag aan en genereert een concept-preview |
| POST | `/song-requests/{songRequest}/checkout` | Stub-checkout en statusupdate |
| POST | `/register` | Registratie |
| POST | `/login` | Login |
| POST | `/forgot-password` | Wachtwoord reset aanvragen |
| POST | `/reset-password` | Wachtwoord reset uitvoeren |
| GET | `/email/verify/{id}/{hash}` | E-mail verificatie |
| POST | `/logout` | Logout, Sanctum token vereist |
| GET | `/me` | Huidige gebruiker, Sanctum token vereist |
| POST | `/email/resend` | Verificatiemail opnieuw sturen |

## Tests

```bash
php artisan test
```

## Productie Na Betaling

Na een succesvolle checkout draait de backend twee stappen:

1. Definitieve lyrics genereren met `LyricsGenerator`, op basis van categorie-templates, herbruikbare rijmregels en intakevelden zoals `anecdotes`, `mustMention`, `avoid`, `tone` en `musicStyle`.
2. Muziek genereren via `MusicProvider`. Met `MUSIC_PROVIDER=stub` wordt nog geen externe API aangeroepen, maar wel een volledige muziekprompt en `music_reference` opgeslagen.

Handmatig opnieuw draaien:

```bash
php artisan songs:produce {songRequestId}
```
