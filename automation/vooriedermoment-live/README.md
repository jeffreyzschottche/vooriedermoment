# Voor Ieder Moment — live aanvragen

Deze map bevat de zelfstandige Keysmith/Suno-flow voor Voor Ieder Moment. De
DistroKid-macro en de oude macro `Vim.nl > suno 4 samples` worden niet gestart
of aangepast.

## Bestanden

- `keysmith-launcher.applescript`: enige actie die in de Keysmith-macro nodig is;
- `workflow.applescript`: bestuurt Firefox via dezelfde macOS Accessibility-aanpak als de werkende DistroKid-macro;
- `workflow-helper.sh`: doet alleen deterministische taken: API, lokale status, OpenAI-cover, audio en upload;
- `keysmith-steps/` en `runner.sh`: oude flow, alleen bewaard als naslag en niet meer gebruikt.

## Lokale configuratie

De eerste Keysmith-actie bevat lokaal deze twee properties:

```applescript
property automationApiKey : "vooriedermoment-pw"
property chatGPTapiKey : "PLAK_HIER_JE_OPENAI_API_KEY"
```

De echte OpenAI-key hoort nooit in Git. De launcher geeft beide waarden via een
tijdelijk bestand met mode `600` door en verwijdert dat bestand direct. De
automation-key wordt lokaal in `.runner/automation-key` bewaard; de OpenAI-key
niet.

## De 15 stappen

1. Lees de actieve Firefox-URL en accepteer alleen een URL onder `/admin/upload/{token}`.
2. Claim via de API exact de geopende order en sla `order.json` en `claim.json` lokaal op.
3. Maak of hervat de ordermap; een afgebroken run pakt nooit stilletjes een andere order.
4. Genereer één vierkante gedeelde cover met OpenAI GPT Image 2 en bewaar `cover.png`.
5. Open `https://suno.com/create` en schakel indien nodig naar Advanced.
6. Vul de definitieve lyrics, Suno-style, unieke titel en — alleen bij expliciete keuze — Male/Female in.
7. Toon een verplichte controle vóór er Suno-credits worden gebruikt.
8. Klik Create, wacht 10 seconden en klik nogmaals Create.
9. Wacht tot vier resultaten met de unieke ordertitel zichtbaar zijn.
10. Open bij de bovenste vier resultaten Share → Copy Link en schrijf iedere link direct naar `samples.json` én `suno-links.json`.
11. Open bij precies die vier resultaten Download → MP3 Audio en koppel iedere nieuwe download meteen aan positie 1–4.
12. Controleer per MP3 bestandsgrootte en een speelduur van minimaal 45 seconden.
13. Knip met ffmpeg vier previews van exact `00:30–00:45`.
14. Upload één gedeelde cover, vier previews, vier titels en vier Suno-links naar de automation-API.
15. Laat de backend de links in `song_samples.suno_source_url` bewaren, markeer de order als voltooid en zet de klantmail in de queue.

## Lokale opslag en herstel

```text
~/Desktop/vooriedermoment-live-aanvragen/
└── order-12-verjaardag-voor-anna/
    ├── order.json
    ├── claim.json
    ├── cover.png
    ├── cover-request.json
    ├── cover-response.json
    ├── samples.json
    ├── suno-links.json
    ├── full/
    │   ├── 1.mp3
    │   └── ...
    ├── previews/
    │   ├── 1.mp3
    │   └── ...
    ├── upload-response.json
    ├── status.json
    └── run.log
```

Een Suno-link wordt meteen na `Copy Link` atomair opgeslagen. Als een latere
download of upload faalt, blijven de reeds gevonden links en bestanden dus
staan. Bij opnieuw starten vanaf dezelfde admin-uploadpagina hervat de helper
dezelfde lokale claim en slaat hij reeds voltooide link/downloadstappen over.

## Veilige controles

Deze checks gebruiken geen Suno-credits en versturen geen mail:

```bash
zsh -n automation/vooriedermoment-live/workflow-helper.sh
automation/vooriedermoment-live/workflow-helper.sh doctor
osacompile -o /tmp/vim-workflow.scpt automation/vooriedermoment-live/workflow.applescript
osacompile -o /tmp/vim-launcher.scpt automation/vooriedermoment-live/keysmith-launcher.applescript
```

De echte run stopt vlak vóór de eerste Create-klik en vraagt dan expliciet om
`Maak 4 nummers`.
