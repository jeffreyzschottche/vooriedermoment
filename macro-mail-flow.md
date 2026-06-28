# Macro + mail-flow (betaalde aanvraag → Suno → release)

Hoe een betaalde aanvraag bij jou terechtkomt en hoe je macro de nummers genereert.
Er wordt **niets naar je computer geschreven** — alles staat op de server en je
**haalt het op via de API**.

## De flow in het kort

1. **Klant vult formulier in en betaalt** (betaling is nu gestubd → altijd "geslaagd").
2. **Server maakt de aanvraag klaar**: genereert lyrics + muziekstijl en bouwt een
   **Suno-klare JSON** (`{categorie}-voor-{naam}-{id}.json`). Die JSON staat op de
   server in `storage/app/orders/` en is ook opvraagbaar via de API.
3. **Jij krijgt een mail-seintje** (naar `ORDERS_NOTIFY_EMAIL`) met de samenvatting +
   de JSON als bijlage. Dat is je signaal: *er staat werk klaar, run de macro.*
4. **Je macro draait** en doet 3 dingen:
   - **Ophalen**: `GET /api/v1/orders/export` → alle betaalde, nog niet opgehaalde aanvragen.
   - **Genereren**: voor elke aanvraag op suno.com `suno.title`, `suno.style` en
     `suno.lyrics` invullen en 4 samples maken.
   - **Bevestigen**: `POST /api/v1/orders/export/ack` met de opgehaalde id's →
     server markeert ze als opgehaald en **verwijdert ze** uit de wachtrij.
5. **4 samples → klant** (binnen 48 uur), klant kiest er 1.
6. **Release**: het gekozen nummer komt binnen 72 uur op **Spotify én Apple Music**
   (beide via DistroKid).

## Endpoints

Alles onder `/api/v1`. Beveiligd met header **`X-Export-Key`** (= `ORDERS_API_KEY` uit `.env`).
Zonder/foute key → `401`.

### 1. Openstaande aanvragen ophalen

```
GET /api/v1/orders/export
Header: X-Export-Key: <ORDERS_API_KEY>
```

Antwoord:

```json
{
  "count": 1,
  "orders": [
    {
      "order_id": 12,
      "filename": "moederdag-voor-anna-12.json",
      "created_at": "2026-06-17T19:56:42+00:00",
      "status": "music_prompt_ready",
      "category": "moederdag",
      "category_title": "Moederdag",
      "customer_email": "klant@voorbeeld.nl",
      "recipient_name": "Anna",
      "price_eur": "9.99",
      "suno": {
        "title": "Moederdag - Anna",
        "style": "dutch party schlager, upbeat, sing-along, festive, female vocals, dutch lyrics, professional production",
        "lyrics": "Lieve mama\nDank voor alles\nJij bent de beste",
        "make_instrumental": false
      },
      "intake": { "recipientName": "Anna", "musicStyle": "Feest / meezinger", "vocals": "Vrouwenstem", "tone": "Warm & persoonlijk" }
    }
  ]
}
```

Voor de macro tellen vooral: `suno.title`, `suno.style`, `suno.lyrics`.

### 2. Bevestigen dat je ze hebt opgehaald

```
POST /api/v1/orders/export/ack
Header: X-Export-Key: <ORDERS_API_KEY>
Body:   { "ids": [12, 13] }
```

Antwoord:

```json
{ "acknowledged": [12, 13], "count": 2 }
```

Hierna verschijnen die aanvragen **niet meer** bij `GET /orders/export` en is het
JSON-bestand op de server verwijderd. Roep `ack` pas aan **nadat** je macro de
nummers daadwerkelijk heeft aangemaakt — zo raak je niets kwijt als de macro halverwege stopt.

## Kant-en-klaar macro-script (curl + jq)

Keysmith kan een shell-stap draaien. Dit script haalt alles op, schrijft elke JSON
lokaal weg (in een tijdelijke werkmap), en bevestigt daarna. Voeg tussen ophalen en
ack je eigen Suno-stappen toe.

```bash
#!/usr/bin/env bash
set -euo pipefail

BASE="https://JOUW-DOMEIN.nl/api/v1"     # of http://127.0.0.1:8000 lokaal
KEY="zet-hier-je-ORDERS_API_KEY"
WORKDIR="$HOME/vim-macro-werk"            # tijdelijke werkmap voor de macro
mkdir -p "$WORKDIR"

# 1. Ophalen
RESP="$(curl -fsS -H "X-Export-Key: $KEY" "$BASE/orders/export")"
COUNT="$(echo "$RESP" | jq '.count')"
echo "Openstaand: $COUNT"
[ "$COUNT" -eq 0 ] && exit 0

# 2. Elke aanvraag wegschrijven als los bestand
echo "$RESP" | jq -c '.orders[]' | while read -r order; do
  fname="$(echo "$order" | jq -r '.filename')"
  echo "$order" > "$WORKDIR/$fname"
  echo "→ $fname"
  # HIER: open suno.com en vul in:
  #   titel  = $(echo "$order" | jq -r '.suno.title')
  #   stijl  = $(echo "$order" | jq -r '.suno.style')
  #   lyrics = $(echo "$order" | jq -r '.suno.lyrics')
done

# 3. Bevestigen (verwijdert ze op de server)
IDS="$(echo "$RESP" | jq -c '[.orders[].order_id]')"
curl -fsS -X POST -H "X-Export-Key: $KEY" -H "Content-Type: application/json" \
  -d "{\"ids\": $IDS}" "$BASE/orders/export/ack"
```

## Instellingen (`.env`)

```
ORDERS_EXPORT_ENABLED=true            # export aan/uit
ORDERS_PATH=                          # leeg = storage/app/orders op de server
ORDERS_NOTIFY_EMAIL=jij@voorbeeld.nl  # mail-seintje per aanvraag (leeg = geen mail)
ORDERS_API_KEY=een-lang-uniek-geheim  # = X-Export-Key voor de macro
```

> Let op: `ORDERS_API_KEY` moet in `.env` op de server hetzelfde zijn als in je macro.
> Voor echte mail (i.p.v. de Mailtrap-sandbox) moet je de `MAIL_*`-gegevens in `.env`
> nog naar een echte SMTP zetten.

## Relevante code

- `app/Services/Orders/OrderExporter.php` — bouwt de JSON, schrijft 'm weg, stuurt de mail.
- `app/Http/Controllers/Api/V1/OrderExportController.php` — `index()` (ophalen) + `ack()` (bevestigen/opruimen).
- `app/Http/Middleware/ExportKeyMiddleware.php` — controleert `X-Export-Key`.
- `app/Mail/NewOrderMail.php` + `resources/views/emails/new-order.blade.php` — het mail-seintje.
- `routes/api.php` — de twee `orders/export`-routes.
- Aangeroepen vanuit `SongRequestController::checkout()` na (stub-)betaling.
