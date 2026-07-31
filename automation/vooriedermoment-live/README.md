# Voor Ieder Moment — live aanvragen

Deze map bevat de kleine, afzonderlijk testbare stappen achter de handmatig
gestarte Keysmith-macro.
De bestaande macro `Vim.nl > suno 4 samples` blijft ongewijzigd en wordt alleen
gebruikt voor de reeds ingeregelde Firefox/Suno-invoer.

## Lokale opslag

Elke aanvraag krijgt een eigen map:

```text
~/Desktop/vooriedermoment-live-aanvragen/
└── order-3-kind-geboren-voor-jeffrey/
    ├── order.json
    ├── claim.json
    ├── full/
    │   ├── 1.mp3
    │   └── ...
    ├── covers/
    │   ├── 1.jpg
    │   └── ...
    ├── previews/
    │   ├── 1.mp3
    │   └── ...
    ├── samples.json
    └── run.log
```

De vier volledige nummers blijven lokaal. Alleen de vier previews van
`00:50–01:05`, covers, titels en Suno-URL's gaan naar de backend.

## Automation-key

Vul in de eerste AppleScript-actie van de Keysmith-macro alleen deze regel in:

```applescript
property automationApiKey : "PLAK_HIER_JE_AUTOMATION_API_KEY"
```

Bij het starten zet stap 1 deze waarde in een lokaal bestand met alleen
lees- en schrijfrechten voor jouw macOS-account. Alle volgende losse acties
gebruiken dat bestand. Je hoeft `install-key.sh` of Sleutelhanger daarom niet
handmatig te gebruiken.

## Runner testen zonder live order

```bash
./automation/vooriedermoment-live/runner.sh doctor
./automation/vooriedermoment-live/runner.sh dry-run
```

`dry-run` claimt niets, gebruikt geen Suno-credits en verstuurt geen mail.

## Productieflow

De Keysmith-macro bestaat uit losse acties:

1. de actieve Firefox-uploadpagina lezen en precies die order via de API claimen;
2. de lokaal opgeslagen order controleren;
3. lokale bridge voor de bestaande VIM/Firefox-macro;
4. Firefox/Suno starten;
5. 180 seconden wachten;
6. vier downloads, covers en metadata controleren;
7. vier previews van `00:50–01:05` maken;
8. vier samples uploaden;
9. dezelfde macro starten voor de volgende order.

De AppleScripts staan afzonderlijk in `keysmith-steps/`. Daardoor is in
Keysmith zichtbaar welke stap faalt en kan iedere stap los worden aangepast.

Bij een fout wordt `/fail` aangeroepen. De order komt dan terug in de wachtrij
en de lokale bestanden blijven staan voor diagnose of herstel.

## Logs

Centraal:

```text
~/Desktop/vooriedermoment-live-aanvragen/macro.log
```

Per order:

```text
~/Desktop/vooriedermoment-live-aanvragen/order-.../run.log
~/Desktop/vooriedermoment-live-aanvragen/order-.../status.json
```

Elke AppleScript-stap logt zijn start/succes of fout. Een mislukte stap stopt de
macro en geeft de live claim vrij.
