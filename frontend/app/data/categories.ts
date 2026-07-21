// Centrale bron voor alle "momenten". Pagina's (momenten/[slug], overzicht, nav)
// en de intake-flow worden hieruit datagedreven opgebouwd.

export type CategoryVariant = 'standard' | 'existing' | 'b2b';

export interface AccentTheme {
  accent: string;
  accentStrong: string;
  accentSoft: string;
  accentInk: string; // toegankelijke tekstkleur óp de accentkleur (wit of donker)
}

export interface IntakeField {
  name: string;
  label: string;
  type: 'text' | 'textarea' | 'select' | 'date' | 'email';
  placeholder?: string;
  help?: string;
  required?: boolean;
  options?: string[];
  span?: 'full' | 'half';
}

export interface SampleTrack {
  title: string;
  subtitle: string;
  spotifyEmbed?: string | null; // null => nette placeholder
}

export interface Faq {
  question: string;
  answer: string;
}

export interface Category {
  slug: string;
  title: string;
  navLabel: string;
  emoji: string;
  variant: CategoryVariant;
  theme: AccentTheme;
  // Categorie-specifieke cover (og:image + visuals). Valt terug op een
  // gegenereerde kleur-cover per slug; zet hier een eigen foto om te overschrijven.
  image?: string;
  kicker: string;
  heroTitle: string;
  heroLead: string;
  intro: string;
  whatYouGet: string[];
  // Voor 'existing': de (nog te delen) Spotify-playlist + "staat jouw naam erbij?"
  playlistEmbed?: string | null;
  existingHook?: string;
  sampleTracks?: SampleTrack[];
  intakeFields: IntakeField[];
  faq: Faq[];
  seoTitle: string;
  seoDescription: string;
  // Optionele eigen route i.p.v. /momenten/[slug] (momenteel ongebruikt).
  route?: string;
}

const themes: Record<string, AccentTheme> = {
  road: { accent: '#1ea67f', accentStrong: '#147a5c', accentSoft: '#e3f6ef', accentInk: '#ffffff' },
  honey: { accent: '#e6a008', accentStrong: '#9c6b00', accentSoft: '#fcf1cf', accentInk: '#2a1d00' },
  brick: { accent: '#d96638', accentStrong: '#a23f17', accentSoft: '#fce7dc', accentInk: '#ffffff' },
  navy: { accent: '#4169e6', accentStrong: '#2a45a8', accentSoft: '#e6ebfc', accentInk: '#ffffff' },
  rose: { accent: '#e0538a', accentStrong: '#b13164', accentSoft: '#fde3ed', accentInk: '#ffffff' },
  sky: { accent: '#1f9fcc', accentStrong: '#17738f', accentSoft: '#e0f4fa', accentInk: '#ffffff' },
  grape: { accent: '#8b54d6', accentStrong: '#653ca8', accentSoft: '#efe4fb', accentInk: '#ffffff' },
  grass: { accent: '#4aa838', accentStrong: '#347526', accentSoft: '#e7f5dd', accentInk: '#ffffff' },
  steel: { accent: '#d97e1a', accentStrong: '#8f5212', accentSoft: '#f7e8d4', accentInk: '#2a1a00' },
};

// Merk-accent (default) — gelijk aan de CSS-vars in tailwind.css. Gebruikt als
// terugval wanneer een context (bv. /aanvraag, checkout) geen categoriekleur heeft.
export const defaultTheme: AccentTheme = {
  accent: '#ff6a4a',
  accentStrong: '#e04a2a',
  accentSoft: '#fff0ec',
  accentInk: '#ffffff',
};

// Zet een AccentTheme om naar de CSS-variabelen waarmee de hele UI zich kleurt.
// Eén bron van waarheid voor per-categorie theming (moment, formulier, funnel).
export function themeVars(theme: AccentTheme | null | undefined): Record<string, string> {
  const t = theme ?? defaultTheme;
  return {
    '--accent': t.accent,
    '--accent-strong': t.accentStrong,
    '--accent-soft': t.accentSoft,
    '--accent-ink': t.accentInk,
  };
}

export const toneOptions = [
  'Vrolijk & uptempo',
  'Warm & persoonlijk',
  'Emotioneel maar niet zwaar',
  'Grappig & ad rem',
  'Stoer & energiek',
  'Feestelijk & uitbundig',
  'Lief & teder',
  'Nostalgisch',
  'Trots & groots',
  'Ontroerend',
  'Speels',
  'Rustig & intiem',
  'Hoopvol',
  'Brutaal met knipoog',
  'Romantisch',
  'Dromerig',
  'Dankbaar',
  'Kwetsbaar',
  'Euforisch',
  'Melancholisch',
  'Motiverend',
  'Ondeugend',
  'Familiair & warm',
  'Kampvuurgevoel',
  'Kroeg / kantine',
  'Festivalgevoel',
  'Filmisch & groots',
  'Klein en oprecht',
  'Laat ons kiezen',
];

export const businessToneOptions = [
  'Stoer & energiek',
  'Trots & ingetogen',
  'Vrolijk & uptempo',
  'Grappig & ad rem',
  'Betrouwbaar & professioneel',
  'Ambachtelijk & warm',
  'Modern & strak',
  'Groots & inspirerend',
  'Nuchter Nederlands',
  'Feestelijk & verbindend',
  'Premium & zakelijk',
  'Menselijk & dichtbij',
  'Innovatief',
  'Teamgevoel',
  'Recruitment-waardig',
  'Jubileumwaardig',
  'Laat ons kiezen',
];

export const musicStyleOptions = [
  'Nederlandstalige pop',
  'Feest / meezinger',
  'Akoestisch en klein',
  'Singer-songwriter',
  'Popballad',
  'Rock / anthem',
  'Urban pop',
  'Hiphop / rapcoupletten',
  'Dance pop',
  'Disco / funk',
  'Country pop',
  'Indie pop',
  'Schlager / après-ski',
  'Koor / stadion',
  'R&B / soul',
  'Piano ballad',
  'Nederlands levenslied',
  'Volksmuziek modern',
  'Reggaeton pop',
  'Afropop',
  'Latin pop',
  'EDM / festival',
  'House',
  'Technopop',
  'Drum & bass pop',
  'Trap pop',
  'Lo-fi pop',
  'Gospel / soulkoor',
  'Musical / theater',
  'Orkestrale pop',
  'Kinderlied / vrolijk',
  'Carnaval',
  'Hardstyle feest',
  'Laat ons kiezen',
];

export const tempoOptions = [
  'Rustig',
  'Medium tempo',
  'Dansbaar',
  'Uptempo',
  'Snel en feestelijk',
  'Opbouwend van rustig naar groot',
  'Laat ons kiezen',
];

export const vocalOptions = [
  'Mannenstem',
  'Vrouwenstem',
  'Duet',
  'Groep / koor',
  'Warme mannenstem',
  'Warme vrouwenstem',
  'Hoge popstem',
  'Lage rustige stem',
  'Rauwe rockstem',
  'Soulvolle stem',
  'Rapcoupletten + gezongen refrein',
  'Kindvriendelijke stem',
  'Familiekoor',
  'Stadionkoor',
  'Koor in refrein',
  'Praat-zang',
  'Instrumentaal intro met zang',
  'Maakt niet uit',
];

// Veelgebruikte intake-velden (hergebruikt over categorieën)
function baseFields(extra: IntakeField[] = []): IntakeField[] {
  return [
    { name: 'recipientName', label: 'Voor wie is het nummer?', type: 'text', placeholder: 'Bijv. Sven, papa, team JO17-1', required: true, span: 'half', help: 'Vul hier één hoofdnaam in. Extra namen kun je met het plusje toevoegen.' },
    { name: 'fromName', label: 'Van wie komt het nummer?', type: 'text', placeholder: 'Bijv. Familie de Vries, het team, de kinderen', span: 'half' },
    ...extra,
    {
      name: 'tone', label: 'Sfeer / toon', type: 'select', required: true, span: 'half',
      options: toneOptions,
    },
    {
      name: 'vocals', label: 'Stem / uitvoering', type: 'select', span: 'half',
      options: vocalOptions,
    },
    {
      name: 'musicStyle', label: 'Genre kiezen', type: 'select', span: 'half',
      options: musicStyleOptions,
      help: 'Kies de richting die het beste bij het moment past. Twijfel je? Laat ons kiezen.',
    },
    {
      name: 'tempo', label: 'Snelheid / tempo', type: 'select', span: 'half',
      options: tempoOptions,
      help: 'Geen exact getal nodig: kies hoe snel het nummer moet voelen.',
    },
    {
      name: 'anecdotes', label: 'Verhaal, anekdotes & inside jokes', type: 'textarea', required: true, span: 'full',
      placeholder: 'Vertel concreet: namen, plaatsen, typische uitspraken, herinneringen, grapjes, gewoontes, wat er absoluut in moet...',
      help: 'Hoe concreter, hoe persoonlijker het nummer. Deze details worden in de tekst verwerkt.',
    },
    {
      name: 'mustMention', label: 'Wat moet er absoluut in?', type: 'textarea', span: 'full',
      placeholder: "Bijv. \"altijd te laat\", \"opa's schuur\", \"de goal in de laatste minuut\", \"bouwen op vertrouwen\"...",
    },
    { name: 'email', label: 'Jouw e-mailadres', type: 'email', placeholder: 'naam@voorbeeld.nl', required: true, span: 'full', help: 'Hier ontvang je de vier samples en updates.' },
  ];
}

export const categories: Category[] = [
  // ── EXISTING (al op Spotify, met playlist + naam-zoeker) ─────────────────
  {
    slug: 'rijbewijs',
    title: 'Rijbewijs gehaald',
    navLabel: 'Rijbewijs gehaald',
    emoji: '🚗',
    variant: 'existing',
    theme: themes.road,
    kicker: 'Geslaagd voor je rijbewijs',
    heroTitle: 'Een nummer voor je pas gehaalde rijbewijs',
    heroLead:
      'Eindelijk dat roze pasje op zak. Ons feestnummer bevat al duizenden namen. Staat die van jou er niet bij? Dan maken we een eigen versie met jouw rijverhaal.',
    intro:
      'Ons rijbewijsnummer staat al op Spotify en Apple Music. Mist jouw naam, of wil je iemand verrassen met een eigen verhaal? Vraag dan een persoonlijke versie aan.',
    whatYouGet: [
      'Vier persoonlijke samples om uit te kiezen',
      'Eén complete versie van jouw favoriet',
      'Met jouw naam (of die van de geslaagde) erin verwerkt',
      'Release op Spotify en Apple Music inbegrepen',
    ],
    playlistEmbed: null,
    existingHook: 'Staat jouw naam al in het nummer?',
    sampleTracks: [
      { title: 'Eindelijk Mijn Rijbewijs', subtitle: 'De originele hit', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'instructor', label: 'Naam rijschool of instructeur (optioneel)', type: 'text', placeholder: 'Bijv. Rijschool Jansen', span: 'half' },
      { name: 'attempts', label: 'Aantal pogingen', type: 'text', placeholder: 'Bijv. in één keer!', span: 'half' },
      { name: 'firstDrive', label: 'Eerste rit of droomauto', type: 'text', placeholder: 'Bijv. meteen naar oma, rondje McDrive, Golf GTI', span: 'half' },
      { name: 'drivingMoment', label: 'Moment van slagen', type: 'text', placeholder: 'Bijv. regen, zenuwen, examinator grapte...', span: 'half' },
    ]),
    faq: [
      { question: 'Het rijbewijs-nummer bestaat toch al?', answer: 'Klopt — er staat een versie op Spotify met heel veel namen. Zit jouw naam er niet bij, of wil je een eigen variant met je eigen verhaal? Dan maken we die op aanvraag.' },
      { question: 'Hoe snel heb ik mijn versie?', answer: 'Binnen 24–72 uur na betaling ontvang je vier samples. Na jouw keuze staat de complete versie binnen 24–72 uur op Spotify en Apple Music.' },
    ],
    seoTitle: 'Rijbewijs gehaald? Een persoonlijk feestnummer',
    seoDescription: 'Geslaagd voor je rijbewijs — vier het met een persoonlijk nummer met jouw naam erin. Al op Spotify, of vraag je eigen versie aan.',
  },
  {
    slug: 'geslaagd',
    title: 'Geslaagd voor je examen',
    navLabel: 'Geslaagd',
    emoji: '🎓',
    variant: 'existing',
    theme: themes.honey,
    kicker: 'Geslaagd!',
    heroTitle: 'Het geslaagd-nummer met jouw naam',
    heroLead:
      'De vlag mag uit. Ons geslaagd-nummer bevat meer dan 10.000 namen. Grote kans dat die van jou ertussen staat — en anders maken we een persoonlijke versie.',
    intro:
      'Ons geslaagd-nummer staat al op Spotify en Apple Music, met meer dan 10.000 namen erin. Zoek hieronder of jouw naam erbij staat. Mist hij? Vraag dan je eigen versie aan.',
    whatYouGet: [
      'Vier persoonlijke samples om uit te kiezen',
      'Eén compleet nummer in jouw favoriete uitvoering',
      'Jouw naam in de tekst',
      'Release op Spotify en Apple Music inbegrepen',
    ],
    playlistEmbed: null,
    existingHook: 'Staat jouw naam tussen de 10.000?',
    sampleTracks: [
      { title: 'Geslaagd! (10.000 Namen)', subtitle: 'Het originele nummer', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'school', label: 'School / opleiding', type: 'text', placeholder: 'Bijv. Stedelijk Gymnasium', span: 'half' },
      { name: 'studyLevel', label: 'Diploma / niveau', type: 'text', placeholder: 'Bijv. VWO, MBO, HBO', span: 'half' },
      { name: 'nextStep', label: 'Wat komt hierna?', type: 'text', placeholder: 'Bijv. tussenjaar, studie rechten, werken', span: 'half' },
      { name: 'examStory', label: 'Examenmoment of stressverhaal', type: 'text', placeholder: 'Bijv. herexamen, nachten blokken, champagne bij de vlag', span: 'half' },
    ]),
    faq: [
      { question: 'Hoe weet ik of mijn naam er al in zit?', answer: 'Gebruik de zoekbalk op deze pagina. Vind je je naam niet, dan vraag je in een paar klikken een eigen versie aan.' },
      { question: 'Kan ik het voor iemand anders aanvragen?', answer: 'Zeker — vul gewoon de naam van de geslaagde in. Perfect als verrassingscadeau.' },
    ],
    seoTitle: 'Geslaagd-nummer met jouw naam',
    seoDescription: 'Geslaagd voor je examen? Vind je naam in ons geslaagd-nummer (10.000+ namen) of vraag je eigen persoonlijke versie aan.',
  },
  {
    slug: 'eigen-huis',
    title: 'Eigen huis gekocht',
    navLabel: 'Eigen huis',
    emoji: '🏡',
    variant: 'existing',
    theme: themes.brick,
    kicker: 'De sleutel is binnen',
    heroTitle: 'Een nummer voor je eigen huis',
    heroLead:
      'De sleutel in handen, dozen in de gang en een plek die écht van jullie is. Maak van dat verhaal een nummer voor de housewarming en lang daarna.',
    intro:
      'Ons nummer over een eigen huis staat al op Spotify en Apple Music. Wil je een versie met jullie namen, nieuwe plek en mooiste verhuisverhalen? Vraag hem hieronder aan.',
    whatYouGet: [
      'Vier persoonlijke samples om uit te kiezen',
      'Eén compleet nummer over jullie nieuwe huis',
      'Namen en details uit jullie verhaal erin',
      'Release op Spotify en Apple Music inbegrepen',
    ],
    playlistEmbed: null,
    existingHook: 'Maak er jullie eigen versie van',
    sampleTracks: [
      { title: 'Ons Eigen Huis', subtitle: 'Het originele nummer', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'place', label: 'Plaats / buurt', type: 'text', placeholder: 'Bijv. Utrecht-Oost', span: 'half' },
      { name: 'firstHome', label: 'Eerste eigen huis?', type: 'select', options: ['Ja, de eerste!', 'Nee, een volgende stap'], span: 'half' },
      { name: 'homeType', label: 'Wat voor plek is het?', type: 'text', placeholder: 'Bijv. jaren 30 woning, appartement, kluswoning', span: 'half' },
      { name: 'favoriteRoom', label: 'Favoriete plek in huis', type: 'text', placeholder: 'Bijv. tuin, keuken, dakterras, mancave', span: 'half' },
    ]),
    faq: [
      { question: 'Is dit voor een housewarming?', answer: 'Perfect daarvoor. We verwerken jullie namen en het verhaal van de nieuwe plek, zodat je hem op het feestje kunt draaien.' },
      { question: 'Kan het over een stel gaan?', answer: 'Ja, vul beide namen in bij het verhaal en we verwerken ze allebei.' },
    ],
    seoTitle: 'Eigen huis gekocht? Een persoonlijk nummer',
    seoDescription: 'Nieuw eigen huis? Vier de sleuteloverdracht met een persoonlijk nummer met jullie namen. Al op Spotify, of vraag je eigen versie aan.',
  },

  // ── STANDARD ─────────────────────────────────────────────────────────────
  {
    slug: 'vaderdag',
    title: 'Vaderdag',
    navLabel: 'Vaderdag',
    emoji: '👔',
    variant: 'standard',
    theme: themes.navy,
    kicker: 'Voor de allerbeste',
    heroTitle: 'Een Vaderdag-nummer dat hij nooit vergeet',
    heroLead: 'Geen sokken of een mok dit jaar. Geef je vader een eigen nummer — over hém, met jullie verhaal erin.',
    intro: 'Vertel ons over je vader: zijn gewoontes, jullie grapjes, dat ene moment. Wij maken er een persoonlijk nummer van.',
    whatYouGet: ['Vier persoonlijke samples om uit te kiezen', 'Eén compleet Vaderdag-nummer', 'Jullie verhaal en inside jokes erin', 'Release op Spotify en Apple Music inbegrepen'],
    intakeFields: baseFields([
      { name: 'nickname', label: 'Hoe noem je hem?', type: 'text', placeholder: 'Bijv. pap, papa, opa', span: 'half' },
      { name: 'hobby', label: 'Zijn hobby of typische dingen', type: 'text', placeholder: 'Bijv. BBQ, voetbal, klussen', span: 'half' },
      { name: 'dadQuote', label: 'Typische uitspraak', type: 'text', placeholder: 'Bijv. “komt goed”, “niet lullen maar poetsen”', span: 'half' },
      { name: 'thanksFor', label: 'Waar wil je hem voor bedanken?', type: 'text', placeholder: 'Bijv. altijd klaarstaan, ritjes, advies', span: 'half' },
    ]),
    faq: [
      { question: 'Op tijd voor Vaderdag?', answer: 'Reken op 24–72 uur voor de vier samples en na je keuze nog eens op 24–72 uur voor de release. Vraag dus ruim voor Vaderdag aan.' },
    ],
    seoTitle: 'Persoonlijk Vaderdag-nummer cadeau',
    seoDescription: 'Verras je vader met een persoonlijk Vaderdag-nummer met jullie eigen verhaal. Snel en betaalbaar.',
  },
  {
    slug: 'moederdag',
    title: 'Moederdag',
    navLabel: 'Moederdag',
    emoji: '💐',
    variant: 'standard',
    theme: themes.rose,
    kicker: 'Voor mama',
    heroTitle: 'Een Moederdag-nummer recht uit het hart',
    heroLead: 'Zeg met muziek wat niet altijd in een kaartje past. Een persoonlijk nummer vol herinneringen, dankbaarheid en jullie eigen woorden.',
    intro: 'Deel jullie mooiste herinneringen en typische momenten. Wij maken er een nummer van dat ze steeds opnieuw kan beluisteren.',
    whatYouGet: ['Vier persoonlijke samples om uit te kiezen', 'Eén compleet Moederdag-nummer', 'Jullie herinneringen verwerkt in de tekst', 'Release op Spotify en Apple Music inbegrepen'],
    intakeFields: baseFields([
      { name: 'nickname', label: 'Hoe noem je haar?', type: 'text', placeholder: 'Bijv. mam, mama, oma', span: 'half' },
      { name: 'memory', label: 'Een dierbare herinnering', type: 'text', placeholder: 'Bijv. zondagse pannenkoeken', span: 'half' },
      { name: 'momTrait', label: 'Wat maakt haar typisch haar?', type: 'text', placeholder: 'Bijv. zorgzaam, chaotisch gezellig, altijd appen', span: 'half' },
      { name: 'thanksFor', label: 'Waar wil je haar voor bedanken?', type: 'text', placeholder: 'Bijv. steun, humor, alles regelen', span: 'half' },
    ]),
    faq: [
      { question: 'Kan het emotioneel én licht?', answer: 'Zeker — kies de sfeer in het formulier. We kunnen ontroerend of juist vrolijk werken.' },
    ],
    seoTitle: 'Persoonlijk Moederdag-nummer cadeau',
    seoDescription: 'Verras je moeder met een persoonlijk Moederdag-nummer met jullie eigen herinneringen. Snel en betaalbaar.',
  },
  {
    slug: 'kind-geboren',
    title: 'Kind geboren',
    navLabel: 'Kind geboren',
    emoji: '👶',
    variant: 'standard',
    theme: themes.sky,
    kicker: 'Welkom kleine',
    heroTitle: 'Een welkomstlied voor jullie kleintje',
    heroLead: 'Een nieuw leven, een nieuwe naam en een verhaal dat pas net begint. Vang dit moment in een persoonlijk geboortenummer.',
    intro: 'Vertel ons de naam, de geboortedatum en wat dit moment zo bijzonder maakt — wij maken er een teder nummer van.',
    whatYouGet: ['Vier persoonlijke samples om uit te kiezen', 'Eén compleet geboortenummer', 'De naam van je kindje in de tekst', 'Release op Spotify en Apple Music inbegrepen'],
    intakeFields: baseFields([
      { name: 'babyName', label: 'Naam van het kindje', type: 'text', placeholder: 'Bijv. Liv', required: true, span: 'half' },
      { name: 'birthDate', label: 'Geboortedatum', type: 'date', span: 'half' },
      { name: 'parents', label: 'Naam van de ouder(s)', type: 'text', placeholder: 'Bijv. Sam en Noor', span: 'half' },
      { name: 'birthDetails', label: 'Bijzondere details', type: 'text', placeholder: 'Bijv. sterrenbeeld, gewicht, grote broer/zus', span: 'half' },
    ]),
    faq: [
      { question: 'Geschikt als kraamcadeau?', answer: 'Heel geschikt. Vul de naam van het kindje en de ouders in, dan maken we er een persoonlijk welkomstlied van.' },
    ],
    seoTitle: 'Persoonlijk geboortenummer voor je baby',
    seoDescription: 'Welkomstlied voor je pasgeboren kindje, met naam en geboortedatum erin verwerkt. Een uniek kraamcadeau.',
  },
  {
    slug: 'verjaardag',
    title: 'Verjaardag',
    navLabel: 'Verjaardag',
    emoji: '🎂',
    variant: 'standard',
    theme: themes.grape,
    kicker: 'Hieperdepiep',
    heroTitle: 'Een verjaardagsnummer op maat',
    heroLead: 'Niet zomaar "Lang zal ze leven". Een eigen verjaardagsnummer met naam, leeftijd en alle inside jokes.',
    intro: 'Vertel ons over de jarige: leeftijd, karakter en de grapjes die alleen jullie snappen. Wij maken er het muzikale hoogtepunt van.',
    whatYouGet: ['Vier persoonlijke samples om uit te kiezen', 'Eén compleet verjaardagsnummer', 'Naam, leeftijd en inside jokes erin', 'Release op Spotify en Apple Music inbegrepen'],
    intakeFields: baseFields([
      { name: 'age', label: 'Welke leeftijd?', type: 'text', placeholder: 'Bijv. 50', span: 'half' },
      { name: 'party', label: 'Soort feest', type: 'text', placeholder: 'Bijv. surprise, kroegavond', span: 'half' },
      { name: 'personality', label: 'Karakter van de jarige', type: 'text', placeholder: 'Bijv. druktemaker, familiemens, levensgenieter', span: 'half' },
      { name: 'partyMoment', label: 'Moment waarop het nummer draait', type: 'text', placeholder: 'Bijv. binnenkomst, speech, dansvloer', span: 'half' },
    ]),
    faq: [
      { question: 'Kan het grappig?', answer: 'Absoluut. Kies “Grappig & ad rem” als sfeer en deel de beste inside jokes in het verhaalveld.' },
    ],
    seoTitle: 'Persoonlijk verjaardagsnummer op maat',
    seoDescription: 'Een uniek verjaardagsnummer met naam, leeftijd en inside jokes. Het hoogtepunt van elk verjaardagsfeest.',
  },
  {
    slug: 'voetbalclubs',
    title: 'Team- of clublied',
    navLabel: 'Teamlied',
    emoji: '⚽',
    variant: 'standard',
    theme: themes.grass,
    kicker: 'Voor teams, kantines en kampioenen',
    heroTitle: 'Een meezinger voor je team of club',
    heroLead: 'Voor de hele ploeg: bij een kampioenschap, seizoensafsluiting, sponsoravond of gewoon omdat jullie een eigen kantinehit verdienen.',
    intro: 'Vertel ons over het team, de clubcultuur, de kleuren en de momenten die iedereen herkent. Wij maken er een meezingbaar teamlied van.',
    whatYouGet: ['Vier persoonlijke samples om uit te kiezen', 'Eén compleet team- of clublied', 'Teamnaam, clubcultuur en momenten in de tekst', 'Release op Spotify en Apple Music inbegrepen'],
    intakeFields: baseFields([
      { name: 'teamType', label: 'Waar gaat het over?', type: 'select', options: ['Hele club', 'Eén team', 'Kampioenswedstrijd', 'Seizoensafsluiting', 'Sponsor / businessclub', 'Supportersgroep'], required: true, span: 'half' },
      { name: 'clubName', label: 'Club- of teamnaam', type: 'text', placeholder: 'Bijv. VV De Spartaan JO17-1', required: true, span: 'half' },
      { name: 'colors', label: 'Clubkleuren', type: 'text', placeholder: 'Bijv. rood-wit', span: 'half' },
      { name: 'players', label: 'Namen die erin mogen', type: 'text', placeholder: 'Bijv. aanvoerder, keeper, trainer, topscorer', span: 'half' },
      { name: 'clubCulture', label: 'Wat typeert de ploeg?', type: 'textarea', placeholder: 'Bijv. derde helft, altijd strijd, slechte warming-up, trainer met vaste uitspraak...', span: 'full' },
      { name: 'chant', label: 'Bestaande yell of leus', type: 'text', placeholder: 'Bijv. “Groen-wit vooruit!”', span: 'full' },
    ]),
    faq: [
      { question: 'Moet het voor één persoon zijn?', answer: 'Nee. Dit formulier is juist bedoeld voor een team, club of supportersgroep. Namen zijn optioneel en alleen handig als ze in het refrein of couplet mogen terugkomen.' },
      { question: 'Voor een amateurclub?', answer: 'Juist leuk. Geef teamnaam, clubcultuur en herkenbare momenten door, dan maken we een meezinger voor in de kantine.' },
    ],
    seoTitle: 'Persoonlijk teamlied of clublied laten maken',
    seoDescription: 'Een uniek voetballied voor je club, team of supportersgroep, met clubnaam, kleuren en herkenbare momenten erin.',
  },

  // ── B2B ──────────────────────────────────────────────────────────────────
  {
    slug: 'bouwbedrijven',
    title: 'Bouwbedrijven',
    navLabel: 'Bouwbedrijven',
    emoji: '🏗️',
    variant: 'b2b',
    theme: themes.steel,
    kicker: 'Voor bouwbedrijven in Nederland',
    heroTitle: 'Een eigen bedrijfsnummer voor jouw bouwbedrijf',
    heroLead:
      'Laat je bedrijf horen. Een gepersonaliseerd nummer over jullie vakmanschap, projecten en mensen — voor op de socials, een jubileum of het bedrijfsfeest.',
    intro:
      'Wij maken op maat een herkenbaar nummer voor bouwbedrijven: van aannemers tot installateurs. Met jullie bedrijfsnaam, kernwaarden en het werk waar je trots op bent.',
    whatYouGet: [
      'Eén compleet bedrijfsnummer in de gekozen uitvoering',
      'Bedrijfsnaam, slogan en kernwaarden verwerkt in de tekst',
      'Overal te delen op Spotify, Apple Music en social media',
      'Inzetbaar voor jubileum, recruitment, beurs of bedrijfsfeest',
    ],
    sampleTracks: [
      { title: 'Wij Bouwen Door', subtitle: 'Sample — stoere bouwhit', spotifyEmbed: null },
      { title: 'Fundament', subtitle: 'Sample — ingetogen & trots', spotifyEmbed: null },
      { title: 'Van Tekening Tot Sleutel', subtitle: 'Sample — uptempo meezinger', spotifyEmbed: null },
      { title: 'Handen Uit De Mouwen', subtitle: 'Sample — energiek werklied', spotifyEmbed: null },
    ],
    intakeFields: [
      { name: 'companyName', label: 'Bedrijfsnaam', type: 'text', placeholder: 'Bijv. Bouwbedrijf Jansen B.V.', required: true, span: 'half' },
      { name: 'contactName', label: 'Contactpersoon', type: 'text', placeholder: 'Naam', required: true, span: 'half' },
      { name: 'discipline', label: 'Specialisme', type: 'select', span: 'half', options: ['Aannemer / nieuwbouw', 'Verbouw / renovatie', 'Installatietechniek', 'Infra / grondwerk', 'Dakdekker', 'Anders'] },
      { name: 'foundingYear', label: 'Opgericht in', type: 'text', placeholder: 'Bijv. 1998', span: 'half' },
      { name: 'slogan', label: 'Slogan of kernwaarden', type: 'text', placeholder: 'Bijv. "Bouwen op vertrouwen"', span: 'full' },
      { name: 'occasion', label: 'Waarvoor gebruik je het nummer?', type: 'select', span: 'half', options: ['Jubileum', 'Bedrijfsfeest', 'Social media / marketing', 'Recruitment', 'Beurs / open dag', 'Anders'] },
      { name: 'tone', label: 'Sfeer / toon', type: 'select', span: 'half', options: businessToneOptions },
      { name: 'musicStyle', label: 'Genre kiezen', type: 'select', span: 'half', options: ['Nederlandstalige pop', 'Rock / anthem', 'Koor / stadion', 'Countrypop', 'Dancepop', 'Hiphop / rapcoupletten', 'Akoestisch en klein', 'Singer-songwriter', 'Popballad', 'Disco / funk', 'House', 'EDM / festival', 'Gospel / soulkoor', 'Orkestrale pop', 'Musical / theater', 'Laat ons kiezen'] },
      { name: 'tempo', label: 'Snelheid / tempo', type: 'select', span: 'half', options: tempoOptions },
      { name: 'anecdotes', label: 'Verhaal van het bedrijf', type: 'textarea', required: true, span: 'full', placeholder: 'Wat maakt jullie bijzonder? Mooiste projecten, het team, de oprichter, typische uitspraken op de bouwplaats...', help: 'Deze details verwerken we in de songtekst.' },
      { name: 'mustMention', label: 'Wat moet er absoluut in?', type: 'textarea', span: 'full', placeholder: 'Projectnamen, slogan, collega’s, locaties of zinnen die terug moeten komen.' },
      { name: 'email', label: 'Zakelijk e-mailadres', type: 'email', placeholder: 'naam@bedrijf.nl', required: true, span: 'full' },
    ],
    faq: [
      { question: 'Krijgen we de rechten om het zakelijk te gebruiken?', answer: 'Je ontvangt één vaste versie die je mag inzetten op je eigen kanalen (social media, website, bedrijfsfeest). Neem voor uitgebreidere commerciële licenties even contact op.' },
      { question: 'Kunnen jullie onze slogan en kernwaarden verwerken?', answer: 'Ja. Een sterke slogan of kernwaarde kan de herkenbare hook van het refrein worden.' },
      { question: 'Hoe snel is het klaar?', answer: 'Binnen 24–72 uur ontvang je vier samples. Na je keuze staat de complete versie binnen 24–72 uur op Spotify en Apple Music. Voor een vaste deadline kun je het best vooraf contact opnemen.' },
    ],
    seoTitle: 'Bedrijfsnummer voor bouwbedrijven in Nederland',
    seoDescription: 'Een gepersonaliseerd bedrijfsnummer voor jouw bouwbedrijf — met bedrijfsnaam, slogan en kernwaarden. Voor jubileum, social media of bedrijfsfeest.',
  },
];

export function getCategory(slug: string): Category | undefined {
  return categories.find((c) => c.slug === slug);
}

export function categoryPath(c: Pick<Category, 'slug' | 'route'>): string {
  return c.route ?? `/momenten/${c.slug}`;
}

// Categorie-cover voor visuals en og:image. Eigen `image` wint; anders de
// gegenereerde kleur-cover (frontend/public/momenten/<slug>.svg).
export function categoryImage(c: Pick<Category, 'slug' | 'image'>): string {
  return c.image ?? `/momenten/${c.slug}.svg`;
}

// Categorieën die in het /momenten-overzicht en de nav verschijnen.
// Bouwbedrijven valt nu ook onder Momenten (geen aparte zakelijke pagina meer).
export const consumerCategories = categories;

// Categorieën die bezoekers als aanvraagkeuze moeten zien.
export const requestCategories = categories;
