// Demo's voor de wisselende speler in de homepage-hero.
// Per moment de drie voorbeeldnummers uit het Spotify-album van die momentpagina
// (zie `playlistEmbed` in categories.ts), elk met een voorbeeldtekst.

export interface HeroDemo {
  slug: string; // categorie-slug (titel + kleur komen uit categories.ts)
  title: string; // exacte tracktitel op Spotify
  uri: string; // spotify:track:…
  lyric: [string, string];
}

const moments: { slug: string; tracks: Omit<HeroDemo, 'slug'>[] }[] = [
  {
    slug: 'geslaagd',
    tracks: [
      { title: 'Geslaagd Voorbeeld 1', uri: 'spotify:track:5zPgf4cPbTwn9bXal5YTPB', lyric: ['Sophie, de vlag gaat uit vandaag,', 'jouw diploma is het antwoord op elke vraag'] },
      { title: 'Geslaagd Voorbeeld 2', uri: 'spotify:track:09YYk6DGohXO9YtPQag7SN', lyric: ['Jesse, die boeken mogen eindelijk dicht,', 'jouw diploma staat hier in het volle licht'] },
      { title: 'Geslaagd Voorbeeld 3', uri: 'spotify:track:6BUqQCpElkj4ksdBwRzTY8', lyric: ['Emma, je deed het, de uitslag is binnen,', 'een nieuw hoofdstuk mag vandaag beginnen'] },
    ],
  },
  {
    slug: 'vaderdag',
    tracks: [
      { title: 'Vaderdag Voorbeeld 1', uri: 'spotify:track:1xQSQK0xaakv2AcKRf40Iz', lyric: ['Pap, jij zegt altijd: komt wel goed,', 'en dankzij jou weet ik hoe dat moet'] },
      { title: 'Vaderdag Voorbeeld 2', uri: 'spotify:track:0CrngArHRZWwhFqhdoXVDc', lyric: ['Jij bij de barbecue met je schort en je tang,', 'pap, jij bent mijn held, mijn leven lang'] },
      { title: 'Vaderdag Voorbeeld 3', uri: 'spotify:track:7EWdINH7mkmMRrw5bIJMU0', lyric: ['Voor elke rit, elk advies, elke grap,', 'dit nummer is voor jou, de beste pap'] },
    ],
  },
  {
    slug: 'kind-geboren',
    tracks: [
      { title: 'Kind geboren Voorbeeld 1', uri: 'spotify:track:2zHMfzfMHrFdGPZznjIQ8M', lyric: ['Welkom, kleine Liv, zo klein en zo zacht,', 'jij bent het wonder waar we op hebben gewacht'] },
      { title: 'Kind geboren Voorbeeld 2', uri: 'spotify:track:0Gka9wJe3UB6bpdScCj4MD', lyric: ['Tien kleine vingers, een neusje zo fijn,', 'Finn, wat zijn we blij dat jij er mag zijn'] },
      { title: 'Kind geboren Voorbeeld 3', uri: 'spotify:track:7bjEYuDlMuLmKut8f6ti01', lyric: ['Slaap maar, Noah, wij zijn dichtbij,', 'ons allergrootste geluk, dat ben jij'] },
    ],
  },
  {
    slug: 'rijbewijs',
    tracks: [
      { title: 'Rijbewijs Voorbeeld 1', uri: 'spotify:track:3JVaBvb6KuYiWsKcbDF7lz', lyric: ['Lotte, de sleutels liggen in je hand,', 'vanaf vandaag rij jij door heel het land'] },
      { title: 'Rijbewijs Voorbeeld 2', uri: 'spotify:track:1rRZGuRjUrxtF00qxEQnzS', lyric: ['Daan, geen instructeur meer naast je in de auto,', 'jij reed die route foutloos, gewoon zo'] },
      { title: 'Rijbewijs Voorbeeld 3', uri: 'spotify:track:7be8fDctv8Vsb8cZFP3Gtg', lyric: ['Noor, je spiegels goed, de bocht perfect gezet,', 'in één keer geslaagd, dat had niemand verwed'] },
    ],
  },
  {
    slug: 'verjaardag',
    tracks: [
      { title: 'Verjaardag Voorbeeld 1', uri: 'spotify:track:6U4CfwSAFnnZAovInqn7wT', lyric: ['Hieperdepiep, Mark wordt vijftig vandaag,', 'en hij danst nog als de beste, als je het mij vraagt'] },
      { title: 'Verjaardag Voorbeeld 2', uri: 'spotify:track:4TLt9kqdapDLEV2HUBPJrU', lyric: ['Lisa, de taart staat klaar, de kaarsjes branden fel,', 'dertig jaar en nog altijd de ster van elk spel'] },
      { title: 'Verjaardag Voorbeeld 3', uri: 'spotify:track:4sNiE87AqxpSJXcg8nIqGC', lyric: ['Opa Kees, tachtig jaar en nog altijd fit,', 'jij bent de reden dat de hele familie hier zit'] },
    ],
  },
  {
    slug: 'moederdag',
    tracks: [
      { title: 'Moederdag Voorbeeld 1', uri: 'spotify:track:3eOvv4oNRvLN0VhaioS9kK', lyric: ['Mam, jij hield me vast als het stormde,', 'jij bent de liefde die mij vormde'] },
      { title: 'Moederdag Voorbeeld 2', uri: 'spotify:track:7zVnFbdAChkBiJvFtAgH4a', lyric: ['Zondag pannenkoeken en jouw lach door het huis,', 'mam, waar jij bent, daar ben ik thuis'] },
      { title: 'Moederdag Voorbeeld 3', uri: 'spotify:track:6qQuxiTdxyqBn2LfdQtVUn', lyric: ['Voor elk appje “ben je veilig thuis?” tot laat,', 'mam, jij bent degene die altijd voor me klaarstaat'] },
    ],
  },
  {
    slug: 'voetbalclubs',
    tracks: [
      { title: 'Team- of clublied Voorbeeld 1', uri: 'spotify:track:4nrUkphTmW0yM15vvmoOoa', lyric: ['Rood-wit, wij geven nooit op,', 'met heel de ploeg staan we samen aan de top'] },
      { title: 'Team- of clublied Voorbeeld 2', uri: 'spotify:track:64ZghAtvPsL52lc0NsIdU5', lyric: ['Na de derde helft zingen we nog door,', 'De Spartaan, dit is ons kampioenskoor'] },
      { title: 'Team- of clublied Voorbeeld 3', uri: 'spotify:track:7BYsqxTaWwldV1MwO42qka', lyric: ['Van de warming-up tot het laatste fluitsignaal,', 'wij zijn één team, wij spelen voor allemaal'] },
    ],
  },
  {
    slug: 'bouwbedrijven',
    tracks: [
      { title: 'Bouwbedrijf Voorbeeld 1', uri: 'spotify:track:2R0dSpox2GlfPlSsq6l9Bf', lyric: ['Van de fundering tot de laatste dakpan,', 'bij Bouwbedrijf Jansen weten ze hoe het kan'] },
      { title: 'Bouwbedrijf Voorbeeld 2', uri: 'spotify:track:71gT7ImBCjUqiyZyQ46iuo', lyric: ['De steiger op en de radio aan,', 'samen laten wij elk project als een huis staan'] },
      { title: 'Bouwbedrijf Voorbeeld 3', uri: 'spotify:track:34kqrChOFVs0cvUBTKCt4D', lyric: ['Helm op en een bak koffie in de keet,', 'wij bouwen het werk waar heel de stad van weet'] },
    ],
  },
];

export const heroDemoSlugs = moments.map((m) => m.slug);

// Om en om per moment, zodat elke wissel een ander moment laat zien:
// alle eerste voorbeelden, dan alle tweede, dan alle derde.
export const heroDemos: HeroDemo[] = [0, 1, 2].flatMap((i) =>
  moments.map((m) => ({ slug: m.slug, ...m.tracks[i]! })),
);
