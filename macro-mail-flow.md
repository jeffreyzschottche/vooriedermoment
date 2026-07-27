# Stripe + macro + mail-flow

De database en API zijn de bron van waarheid. Er worden geen queue-JSON-bestanden
op de tijdelijke containerdisk gezet.

## Productieflow

1. De frontend maakt een aanvraag aan.
2. `POST /api/v1/song-requests/{id}/checkout` maakt een Stripe Checkout Session.
3. De browser gaat naar de beveiligde Stripe-betaalpagina.
4. Alleen een geldig ondertekend Stripe-webhook markeert de aanvraag als betaald.
5. Een queue-job maakt de definitieve lyrics en Suno-payload.
6. De aanvraag krijgt `automation_status=ready`.
7. `ORDERS_NOTIFY_EMAIL` ontvangt één mail met samenvatting en JSON-bijlage.
8. De Mac claimt één order en maakt vier nummers in Suno.
9. De Mac uploadt vier previews, volledige audiobestanden, covers en Suno-URL's.
10. De backend slaat alles op, zet de order op `samples_ready` en mailt de klant één keer.

## Live Stripe-configuratie

Zet in Coolify:

```env
PAYMENT_PROVIDER=stripe
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
FRONTEND_URL=https://vooriedermoment.nl
APP_URL=https://api.vooriedermoment.nl
```

Voeg in Stripe een webhookbestemming toe:

```text
https://api.vooriedermoment.nl/api/v1/payments/stripe/webhook
```

Selecteer deze events:

- `checkout.session.completed`
- `checkout.session.async_payment_succeeded`

Kopieer daarna de signing secret van de webhookbestemming naar
`STRIPE_WEBHOOK_SECRET`. De browser krijgt nooit de geheime Stripe-key en kan
zelf geen order als betaald markeren.

## Live mail-, queue- en automation-configuratie

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_...
MAIL_FROM_ADDRESS=noreply@vooriedermoment.nl
MAIL_FROM_NAME="Voor Ieder Moment"
ORDERS_NOTIFY_EMAIL=orders@voorbeeld.nl

QUEUE_CONNECTION=database
AUTOMATION_API_KEY=een-lang-uniek-geheim
AUTOMATION_CLAIM_TTL_MINUTES=60
```

De Dockercontainer start zelf een Laravel queue worker.

## Persistente sample-opslag

Optie 1, lokale opslag:

```env
SAMPLES_STORAGE_DISK=local
SAMPLES_RETENTION_DAYS=14
```

Koppel dan in Coolify een persistent volume aan:

```text
/var/www/html/storage/app/private
```

Optie 2, S3/R2:

```env
SAMPLES_STORAGE_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=auto
AWS_BUCKET=...
AWS_ENDPOINT=https://...
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Zonder persistent volume of S3/R2 verdwijnen audiobestanden bij een nieuwe
deployment.

## Automation-authenticatie

Alle automation-routes gebruiken:

```text
X-Automation-Key: <AUTOMATION_API_KEY>
```

Een geclaimde order krijgt daarnaast een tijdelijke `claim_token`. Stuur die bij
vervolgrequests mee als:

```text
X-Claim-Token: <claim_token>
```

Bewaar `AUTOMATION_API_KEY` in macOS Keychain, niet hardcoded in de macro.

## 1. Eén order claimen

```http
POST /api/v1/automation/orders/claim
Content-Type: application/json
X-Automation-Key: ...

{"worker_id":"studio-mac"}
```

Voorbeeld:

```json
{
  "data": {
    "order": {
      "order_id": 12,
      "filename": "verjaardag-voor-anna-12.json",
      "status": "music_prompt_ready",
      "category": "verjaardag",
      "category_title": "Verjaardag",
      "customer_email": "klant@example.nl",
      "recipient_name": "Anna",
      "price_eur": "9.99",
      "suno": {
        "title": "Verjaardag - Anna",
        "style": "dutch pop, catchy, radio-friendly, female vocals, dutch lyrics, professional production",
        "lyrics": "...",
        "make_instrumental": false
      },
      "intake": {}
    },
    "claim_token": "...",
    "claimed_by": "studio-mac",
    "claim_expires_at": "2026-07-27T21:30:00+00:00"
  }
}
```

Als er niets klaarstaat is `data` gelijk aan `null`. Een actieve claim voorkomt
dat een tweede Mac dezelfde order krijgt. Na het verlopen van de claim kan de
order opnieuw worden opgepakt.

## 2. Vier samples uploaden

```http
POST /api/v1/automation/orders/{id}/samples
Content-Type: multipart/form-data
X-Automation-Key: ...
X-Claim-Token: ...
```

Per positie 1 tot en met 4 zijn verplicht:

- `samples[n][position]`
- `samples[n][title]`
- `samples[n][suno_source_url]`
- `samples[n][preview]` — mp3, maximaal 20 MB
- `samples[n][original]` — mp3/wav/m4a, maximaal 100 MB
- `samples[n][cover]` — jpg/png/webp, maximaal 10 MB

Voorbeeld met `curl`:

```bash
curl -fsS -X POST \
  -H "X-Automation-Key: $AUTOMATION_KEY" \
  -H "X-Claim-Token: $CLAIM_TOKEN" \
  -F 'samples[0][position]=1' \
  -F 'samples[0][title]=Versie 1' \
  -F 'samples[0][suno_source_url]=https://suno.com/song/...' \
  -F 'samples[0][preview]=@preview-1.mp3' \
  -F 'samples[0][original]=@original-1.mp3' \
  -F 'samples[0][cover]=@cover-1.jpg' \
  -F 'samples[1][position]=2' \
  -F 'samples[1][title]=Versie 2' \
  -F 'samples[1][suno_source_url]=https://suno.com/song/...' \
  -F 'samples[1][preview]=@preview-2.mp3' \
  -F 'samples[1][original]=@original-2.mp3' \
  -F 'samples[1][cover]=@cover-2.jpg' \
  -F 'samples[2][position]=3' \
  -F 'samples[2][title]=Versie 3' \
  -F 'samples[2][suno_source_url]=https://suno.com/song/...' \
  -F 'samples[2][preview]=@preview-3.mp3' \
  -F 'samples[2][original]=@original-3.mp3' \
  -F 'samples[2][cover]=@cover-3.jpg' \
  -F 'samples[3][position]=4' \
  -F 'samples[3][title]=Versie 4' \
  -F 'samples[3][suno_source_url]=https://suno.com/song/...' \
  -F 'samples[3][preview]=@preview-4.mp3' \
  -F 'samples[3][original]=@original-4.mp3' \
  -F 'samples[3][cover]=@cover-4.jpg' \
  "https://api.vooriedermoment.nl/api/v1/automation/orders/$ORDER_ID/samples"
```

Alleen nadat database-opslag en alle bestanden zijn gelukt:

- krijgt de order `status=samples_ready`;
- krijgt automation `status=completed`;
- wordt één `SamplesReadyMail` voor de klant in de queue gezet.

Een retry met dezelfde claimtoken na een al geslaagde upload geeft de bestaande
vier samples terug en verstuurt geen tweede klantmail.

## 3. Een mislukte run vrijgeven

```http
POST /api/v1/automation/orders/{id}/fail
Content-Type: application/json
X-Automation-Key: ...
X-Claim-Token: ...

{"error":"Suno was tijdelijk niet bereikbaar"}
```

De order wordt weer `ready` en kan bij de volgende run opnieuw worden geclaimd.

## 4. Zonder bestanden handmatig afronden

`POST /api/v1/automation/orders/{id}/complete` bestaat voor een toekomstige
workflow zonder sample-upload. In de normale Suno-flow is dit endpoint niet
nodig: een succesvolle upload rondt de claim automatisch af.
