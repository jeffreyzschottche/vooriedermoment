<?php

/*
|--------------------------------------------------------------------------
| Lyrics-templates & herbruikbare rijm-batch
|--------------------------------------------------------------------------
|
| Elk nummer wordt opgebouwd uit:
|   - intro/verse/chorus : kant-en-klare, al rijmende bouwstenen met
|                          {placeholders} die uit de intake worden gevuld
|                          (placeholders => intake-veldnamen via 'placeholders').
|   - ai                 : één gepersonaliseerd, rijmend couplet dat via de
|                          AiManager wordt aangevuld. Zonder API-key valt de
|                          generator terug op 'fallback' (ook al rijmend).
|
| De LyricsGenerator zet dit samen tot:
|   [Couplet 1] (verse) -> [Refrein] (chorus) -> [Couplet 2] (ai) -> [Refrein]
|
*/

return [

    // Vangnet voor onbekende/eigen gelegenheden ('anders').
    'default' => [
        'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'gelegenheid' => 'occasion'],
        'defaults' => ['naam' => 'jij', 'van' => 'wij', 'gelegenheid' => 'dit moment'],
        'intro' => ['Vandaag is er een reden voor een lied,'],
        'verse' => [
            'Een moment om te vieren, vergeet het toch niet,',
            'Voor {naam} klinkt het hier, recht uit ons gemoed.',
        ],
        'chorus' => [
            'Dit is voor {naam}, dit moment is van jou,',
            'Een nummer vol kleur in plaats van grijs en grauw.',
            'Wij zingen het mee, luid en oprecht,',
            'Voor {naam} is dit lied, speciaal en echt.',
        ],
        'ai' => [
            'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels voor {gelegenheid}, ter ere van {naam}. Verwerk dit verhaal indien passend: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
            'fallback' => [
                'Van {van} een lied, gemeend en spontaan,',
                'Voor {naam} die vandaag vooraan mag staan.',
            ],
        ],
    ],

    'categories' => [

        'geslaagd' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'school' => 'school'],
            'defaults' => ['naam' => 'jij', 'van' => 'wij', 'school' => 'school'],
            'intro' => ['De boeken dicht, de zenuwen voorbij,'],
            'verse' => [
                'Maandenlang geblokt, soms tot diep in de nacht,',
                '{naam} hield vol en heeft het volbracht.',
            ],
            'chorus' => [
                'Geslaagd, geslaagd, de vlag mag uit vandaag!',
                '{naam} heeft het gehaald, geen enkele vraag.',
                'Het diploma is binnen, de pet vliegt omhoog,',
                'Voor {naam} schijnt vandaag een gloednieuwe boog.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels voor wie geslaagd is. Naam: {naam}, school: {school}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Op {school} werd er hard voor gewerkt,',
                    'En {naam} heeft dat dubbel en dwars versterkt.',
                ],
            ],
        ],

        'rijbewijs' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'rijschool' => 'instructor'],
            'defaults' => ['naam' => 'jij', 'van' => 'wij', 'rijschool' => 'de rijschool'],
            'intro' => ['Het roze pasje zit eindelijk op zak,'],
            'verse' => [
                'Spiegel, schakelen, koppeling en gas,',
                '{naam} reed het examen alsof het niets was.',
            ],
            'chorus' => [
                'Geslaagd voor het rijbewijs, de weg is nu vrij!',
                '{naam} achter het stuur — rij maar lekker voorbij.',
                'Sleutel in het contact en het raampje omlaag,',
                'Voor {naam} is de snelweg een feest elke dag.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels over het halen van het rijbewijs. Naam: {naam}, rijschool: {rijschool}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Bij {rijschool} werd er flink geoefend en gevloekt,',
                    'Maar {naam} heeft de weg naar het pasje gezocht.',
                ],
            ],
        ],

        'eigen-huis' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'plaats' => 'place'],
            'defaults' => ['naam' => 'jij', 'van' => 'wij', 'plaats' => 'de stad'],
            'intro' => ['De sleutel draait om, de drempel voorbij,'],
            'verse' => [
                'Dozen in de gang en een muur die nog wacht,',
                '{naam} heeft een eigen plek, gewenst en bedacht.',
            ],
            'chorus' => [
                'Een eigen huis, een dak dat van jou is!',
                'Voor {naam} een thuis waar het altijd weer fris is.',
                'In {plaats} staat een deur met jouw naam op de mat,',
                'Voor {naam} begint hier een gloednieuw pad.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels over een nieuw eigen huis. Naam: {naam}, plaats: {plaats}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'In {plaats} klinkt het glas voor een gloednieuw begin,',
                    'En {naam} richt vol liefde elke kamer in.',
                ],
            ],
        ],

        'vaderdag' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'koosnaam' => 'nickname'],
            'defaults' => ['naam' => 'pap', 'van' => 'wij', 'koosnaam' => 'pap'],
            'intro' => ['Een dag in het jaar speciaal voor jou,'],
            'verse' => [
                'Voor wijze raad en een knuffel zo trouw,',
                'Bedankt lieve {koosnaam}, voor alles wat jij bouwt.',
            ],
            'chorus' => [
                'Voor {koosnaam} dit lied, op Vaderdag luid,',
                'De beste die er is, dat zingen wij uit.',
                'Een held zonder cape maar met handen van goud,',
                'Voor {naam} dit nummer, met liefde gebouwd.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels voor Vaderdag, voor {koosnaam}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Of het nou klussen is of een grap op zijn tijd,',
                    'Voor {koosnaam} staan wij altijd weer klaar en bereid.',
                ],
            ],
        ],

        'moederdag' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'koosnaam' => 'nickname'],
            'defaults' => ['naam' => 'mam', 'van' => 'wij', 'koosnaam' => 'mam'],
            'intro' => ['Lieve {koosnaam}, vandaag draait het om jou,'],
            'verse' => [
                'Voor eindeloos geduld en een liefde zo trouw,',
                'Dit lied is voor jou, gemeend en oprecht trouw.',
            ],
            'chorus' => [
                'Voor {koosnaam} dit lied, op Moederdag zacht,',
                'De warmte die jij geeft, dag en ook nacht.',
                'Een hart zo groot, een glimlach van goud,',
                'Voor {naam} dit nummer, met liefde gebouwd.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende Nederlandse liedregels voor Moederdag, voor {koosnaam}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Van een troostend woord tot een bord op tafel klaar,',
                    'Voor {koosnaam} staan wij vandaag voor elkaar.',
                ],
            ],
        ],

        'kind-geboren' => [
            'placeholders' => ['naam' => 'babyName', 'van' => 'fromName'],
            'defaults' => ['naam' => 'kleintje', 'van' => 'wij'],
            'intro' => ['Tien kleine vingers, tien teentjes zo klein,'],
            'verse' => [
                'Een wonder geboren, zo puur en zo fijn,',
                'Welkom lief {naam}, je mag er helemaal zijn.',
            ],
            'chorus' => [
                'Welkom {naam}, ons kleine geluk,',
                'De wereld is mooier, geen enkel stuk druk.',
                'Een sterretje straalt sinds de dag dat je kwam,',
                'Voor {naam} dit lied, zo zacht als een lam.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende, tedere Nederlandse liedregels voor de geboorte van baby {naam}. Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Slaap zacht klein wonder, de nacht is van jou,',
                    'Voor {naam} onze liefde, voor altijd en trouw.',
                ],
            ],
        ],

        'verjaardag' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'leeftijd' => 'age'],
            'defaults' => ['naam' => 'jij', 'van' => 'wij', 'leeftijd' => 'weer een jaar'],
            'intro' => ['Hieperdepiep, de slingers gaan op,'],
            'verse' => [
                'De taart staat al klaar met een kaarsje erbovenop,',
                'Voor {naam} klinkt het lied, helemaal top.',
            ],
            'chorus' => [
                'Lang zal {naam} leven, in de gloria vandaag!',
                'Geen standaardliedje, maar eentje naar wens en vraag.',
                'Het glas gaat omhoog, de muziek staat hard aan,',
                'Voor {naam} dit feestje, kom maar lekker meegaan.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende, vrolijke Nederlandse liedregels voor de verjaardag van {naam} ({leeftijd}). Verwerk dit verhaal/inside jokes als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Of het nou {leeftijd} is of gewoon weer een jaar,',
                    'Voor {naam} staan wij met confetti al klaar.',
                ],
            ],
        ],

        'voetbalclubs' => [
            'placeholders' => ['naam' => 'recipientName', 'van' => 'fromName', 'club' => 'clubName', 'kleuren' => 'colors'],
            'defaults' => ['naam' => 'het team', 'van' => 'de fans', 'club' => 'de club', 'kleuren' => 'onze kleuren'],
            'intro' => ['De tribune staat op, de sjaals in de lucht,'],
            'verse' => [
                'Voor {club} klinkt het hard, geen enkele zucht,',
                'In {kleuren} gehuld jagen wij op de winst en de klucht.',
            ],
            'chorus' => [
                'Hup {club}, vooruit, de overwinning tegemoet!',
                'In {kleuren} op de mat, het zit ons in het bloed.',
                'Van de eerste fluit tot het laatste signaal,',
                'Voor {club} zingen wij, keer op keer, allemaal.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende, meezingbare Nederlandse voetballiedregels voor {club} (kleuren: {kleuren}). Verwerk dit verhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Met {kleuren} op de borst en de kop fier omhoog,',
                    'Staat {club} als een huis, recht door de boog.',
                ],
            ],
        ],

        'bouwbedrijven' => [
            'placeholders' => ['bedrijf' => 'companyName', 'slogan' => 'slogan', 'van' => 'contactName'],
            'defaults' => ['bedrijf' => 'het bedrijf', 'slogan' => 'bouwen op vertrouwen', 'van' => 'het team'],
            'intro' => ['Vanaf de eerste paal tot de laatste steen,'],
            'verse' => [
                'Bij {bedrijf} staat het vakmanschap als een been,',
                'Samen bouwen wij door, nooit alleen.',
            ],
            'chorus' => [
                '{bedrijf}, wij bouwen door, regen of zon!',
                'Van fundament tot dak, zoals het ooit begon.',
                'Handen uit de mouwen, vakwerk dat staat,',
                'Bij {bedrijf} is het "{slogan}" geen loze praat.',
            ],
            'ai' => [
                'instruction' => 'Schrijf precies twee rijmende, stoere Nederlandse liedregels voor bouwbedrijf {bedrijf} (slogan: {slogan}). Verwerk dit bedrijfsverhaal als het past: "{anecdotes}". Toon: {tone}. Geef alleen de twee regels.',
                'fallback' => [
                    'Van tekening tot sleutel, met trots en met daad,',
                    'Bij {bedrijf} weet je: het werk dat staat.',
                ],
            ],
        ],

    ],

];
