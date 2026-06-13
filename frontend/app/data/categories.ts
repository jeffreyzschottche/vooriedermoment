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
  // Aparte route i.p.v. /momenten/[slug] (alleen b2b)
  route?: string;
}

const themes: Record<string, AccentTheme> = {
  road: { accent: '#2f9e7f', accentStrong: '#1f6f59', accentSoft: '#dcf3eb', accentInk: '#ffffff' },
  honey: { accent: '#e8a317', accentStrong: '#a86f00', accentSoft: '#fdf0cf', accentInk: '#2a1d00' },
  brick: { accent: '#cf6a3f', accentStrong: '#9c451f', accentSoft: '#fbe6da', accentInk: '#ffffff' },
  navy: { accent: '#3f6bd6', accentStrong: '#2a4aa3', accentSoft: '#e2e9fb', accentInk: '#ffffff' },
  rose: { accent: '#db5a8b', accentStrong: '#aa3a66', accentSoft: '#fce1ec', accentInk: '#ffffff' },
  sky: { accent: '#46a7c9', accentStrong: '#2c7a96', accentSoft: '#dff2f8', accentInk: '#ffffff' },
  grape: { accent: '#8a5cd1', accentStrong: '#643ca3', accentSoft: '#ece1fa', accentInk: '#ffffff' },
  grass: { accent: '#4c9c3a', accentStrong: '#356d28', accentSoft: '#e3f3da', accentInk: '#ffffff' },
  steel: { accent: '#d9821f', accentStrong: '#8f5212', accentSoft: '#f6e7d2', accentInk: '#2a1a00' },
};

// Veelgebruikte intake-velden (hergebruikt over categorieën)
function baseFields(extra: IntakeField[] = []): IntakeField[] {
  return [
    { name: 'recipientName', label: 'Voor wie is het nummer?', type: 'text', placeholder: 'Bijv. Sven', required: true, span: 'half' },
    { name: 'fromName', label: 'Van wie is het?', type: 'text', placeholder: 'Bijv. Familie de Vries', span: 'half' },
    ...extra,
    {
      name: 'tone', label: 'Sfeer / toon', type: 'select', required: true, span: 'half',
      options: ['Vrolijk & uptempo', 'Emotioneel & ontroerend', 'Grappig & ad rem', 'Stoer & energiek'],
    },
    {
      name: 'vocals', label: 'Stem', type: 'select', span: 'half',
      options: ['Mannenstem', 'Vrouwenstem', 'Duet', 'Maakt niet uit'],
    },
    {
      name: 'anecdotes', label: 'Verhaal, anekdotes & inside jokes', type: 'textarea', required: true, span: 'full',
      placeholder: 'Vertel wat dit moment of deze persoon uniek maakt — namen, plaatsen, grapjes, gewoontes...',
      help: 'Hoe concreter, hoe persoonlijker het nummer. Deze details worden in de tekst verwerkt.',
    },
    { name: 'email', label: 'Jouw e-mailadres', type: 'email', placeholder: 'naam@voorbeeld.nl', required: true, span: 'full', help: 'Hier leveren we het nummer af.' },
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
      'Eindelijk dat roze pasje op zak. Wij hebben er al een feestnummer over gemaakt — met duizenden namen erin. Staat die van jou er niet bij? Dan maken we hem alsnog.',
    intro:
      'Onze rijbewijs-hit staat al op Spotify en Apple Music. Veel namen zitten er al in. Mist die van jou (of van iemand die je wil verrassen)? Vraag een eigen versie aan.',
    whatYouGet: [
      'Eén afgewerkt nummer, klaar om te delen',
      'Met jouw naam (of die van de geslaagde) erin verwerkt',
      'Te beluisteren en delen via een directe link',
    ],
    playlistEmbed: null,
    existingHook: 'Staat jouw naam al in het nummer?',
    sampleTracks: [
      { title: 'Eindelijk Mijn Rijbewijs', subtitle: 'De originele hit', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'instructor', label: 'Naam rijschool of instructeur (optioneel)', type: 'text', placeholder: 'Bijv. Rijschool Jansen', span: 'half' },
      { name: 'attempts', label: 'Aantal pogingen', type: 'text', placeholder: 'Bijv. in 1x!', span: 'half' },
    ]),
    faq: [
      { question: 'Het rijbewijs-nummer bestaat toch al?', answer: 'Klopt — er staat een versie op Spotify met heel veel namen. Zit jouw naam er niet bij, of wil je een eigen variant met je eigen verhaal? Dan maken we die op aanvraag.' },
      { question: 'Hoe snel heb ik mijn versie?', answer: 'Een persoonlijke versie leveren we doorgaans binnen enkele dagen digitaal aan.' },
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
      'De vlag mag uit. We hebben een geslaagd-nummer met meer dan 10.000 namen. Grote kans dat die van jou er al in zit — en zo niet, dan vul je hem zo aan.',
    intro:
      'Ons geslaagd-nummer staat al op Spotify en Apple Music, met meer dan 10.000 namen erin. Zoek hieronder of jouw naam erbij staat. Mist hij? Vraag dan je eigen versie aan.',
    whatYouGet: [
      'Eén afgewerkt geslaagd-nummer om te delen',
      'Jouw naam in de tekst',
      'Direct deelbaar met klasgenoten en familie',
    ],
    playlistEmbed: null,
    existingHook: 'Staat jouw naam tussen de 10.000?',
    sampleTracks: [
      { title: 'Geslaagd! (10.000 Namen)', subtitle: 'Het originele nummer', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'school', label: 'School / opleiding', type: 'text', placeholder: 'Bijv. Stedelijk Gymnasium', span: 'half' },
      { name: 'studyLevel', label: 'Diploma / niveau', type: 'text', placeholder: 'Bijv. VWO, MBO, HBO', span: 'half' },
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
      'Eerste eigen plek, een dak boven je hoofd dat écht van jou is. Daar hoort muziek bij. We hebben er al een nummer over — vul je eigen verhaal aan.',
    intro:
      'Ons "eigen huis"-nummer staat al op Spotify en Apple Music. Wil je een versie met jullie namen en jullie nieuwe adres-verhaal? Vraag hem hieronder aan.',
    whatYouGet: [
      'Eén afgewerkt nummer over jullie nieuwe huis',
      'Namen en details uit jullie verhaal erin',
      'Ideaal als housewarming-verrassing',
    ],
    playlistEmbed: null,
    existingHook: 'Maak er jullie eigen versie van',
    sampleTracks: [
      { title: 'Ons Eigen Huis', subtitle: 'Het originele nummer', spotifyEmbed: null },
    ],
    intakeFields: baseFields([
      { name: 'place', label: 'Plaats / buurt', type: 'text', placeholder: 'Bijv. Utrecht-Oost', span: 'half' },
      { name: 'firstHome', label: 'Eerste eigen huis?', type: 'select', options: ['Ja, de eerste!', 'Nee, een volgende stap'], span: 'half' },
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
    whatYouGet: ['Eén persoonlijk Vaderdag-nummer', 'Jullie verhaal en inside jokes erin', 'Klaar om te draaien op Vaderdag'],
    intakeFields: baseFields([
      { name: 'nickname', label: 'Hoe noem je hem?', type: 'text', placeholder: 'Bijv. pap, papa, opa', span: 'half' },
      { name: 'hobby', label: 'Zijn hobby of typische dingen', type: 'text', placeholder: 'Bijv. BBQ, voetbal, klussen', span: 'half' },
    ]),
    faq: [
      { question: 'Op tijd voor Vaderdag?', answer: 'Vraag je nummer een paar dagen van tevoren aan, dan staat het ruim op tijd klaar.' },
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
    heroLead: 'Zeg het met muziek. Een persoonlijk nummer over je moeder raakt dieper dan welk boeket dan ook.',
    intro: 'Deel jullie verhaal en herinneringen, dan gieten wij het in een nummer dat ze koestert.',
    whatYouGet: ['Eén persoonlijk Moederdag-nummer', 'Jullie herinneringen verwerkt in de tekst', 'Een cadeau om te bewaren'],
    intakeFields: baseFields([
      { name: 'nickname', label: 'Hoe noem je haar?', type: 'text', placeholder: 'Bijv. mam, mama, oma', span: 'half' },
      { name: 'memory', label: 'Een dierbare herinnering', type: 'text', placeholder: 'Bijv. zondagse pannenkoeken', span: 'half' },
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
    heroTitle: 'Een nummer voor jullie pasgeboren wonder',
    heroLead: 'Het mooiste nieuws verdient een eigen welkomstlied. Met de naam van je kindje en jullie liefde erin.',
    intro: 'Vertel ons de naam, de geboortedatum en wat dit moment zo bijzonder maakt — wij maken er een teder nummer van.',
    whatYouGet: ['Eén persoonlijk geboortenummer', 'De naam van je kindje erin', 'Een blijvende herinnering aan dag één'],
    intakeFields: baseFields([
      { name: 'babyName', label: 'Naam van het kindje', type: 'text', placeholder: 'Bijv. Liv', required: true, span: 'half' },
      { name: 'birthDate', label: 'Geboortedatum', type: 'date', span: 'half' },
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
    intro: 'Vertel ons over de jarige: leeftijd, karakter, en de grapjes die alleen jullie snappen. Wij maken het feestje compleet.',
    whatYouGet: ['Eén persoonlijk verjaardagsnummer', 'Naam, leeftijd en inside jokes erin', 'Hét moment van het feest'],
    intakeFields: baseFields([
      { name: 'age', label: 'Welke leeftijd?', type: 'text', placeholder: 'Bijv. 50', span: 'half' },
      { name: 'party', label: 'Soort feest', type: 'text', placeholder: 'Bijv. surprise, kroegavond', span: 'half' },
    ]),
    faq: [
      { question: 'Kan het grappig?', answer: 'Absoluut. Kies "Grappig & ad rem" als sfeer en stop je inside jokes in het verhaal-veld.' },
    ],
    seoTitle: 'Persoonlijk verjaardagsnummer op maat',
    seoDescription: 'Een uniek verjaardagsnummer met naam, leeftijd en inside jokes. Het hoogtepunt van elk verjaardagsfeest.',
  },
  {
    slug: 'voetbalclubs',
    title: 'Voetbalclubs',
    navLabel: 'Voetbalclubs',
    emoji: '⚽',
    variant: 'standard',
    theme: themes.grass,
    kicker: 'Voor de fans',
    heroTitle: 'Een clublied voor jouw team',
    heroLead: 'Van kampioenslied tot spreekkoor: een eigen nummer voor je club, je team of je favoriete speler.',
    intro: 'Vertel ons over de club, de kleuren, de aartsrivaal en de helden van het veld. Wij maken er een meezinger van.',
    whatYouGet: ['Eén persoonlijk clublied', 'Clubnaam, kleuren en spelers erin', 'Klaar voor de tribune of de kantine'],
    intakeFields: baseFields([
      { name: 'clubName', label: 'Naam van de club / team', type: 'text', placeholder: 'Bijv. VV De Spartaan', required: true, span: 'half' },
      { name: 'colors', label: 'Clubkleuren', type: 'text', placeholder: 'Bijv. rood-wit', span: 'half' },
    ]),
    faq: [
      { question: 'Voor een amateurclub?', answer: 'Juist leuk. Geef de teamnaam en wat namen door, dan maken we een meezinger voor in de kantine.' },
    ],
    seoTitle: 'Persoonlijk clublied voor je voetbalclub',
    seoDescription: 'Een uniek voetballied voor je club of team, met clubnaam, kleuren en spelers erin. Klaar voor de tribune.',
  },

  // ── B2B ──────────────────────────────────────────────────────────────────
  {
    slug: 'bouwbedrijven',
    title: 'Bouwbedrijven',
    navLabel: 'Bouwbedrijven',
    emoji: '🏗️',
    variant: 'b2b',
    route: '/zakelijk/bouwbedrijven',
    theme: themes.steel,
    kicker: 'Voor bouwbedrijven in Nederland',
    heroTitle: 'Een eigen bedrijfsnummer voor jouw bouwbedrijf',
    heroLead:
      'Laat je bedrijf horen. Een gepersonaliseerd nummer over jullie vakmanschap, projecten en mensen — voor op de socials, een jubileum of het bedrijfsfeest.',
    intro:
      'Wij maken op maat een herkenbaar nummer voor bouwbedrijven: van aannemers tot installateurs. Met jullie bedrijfsnaam, kernwaarden en het werk waar je trots op bent.',
    whatYouGet: [
      'Eén gepersonaliseerd bedrijfsnummer (1 vaste versie)',
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
      { name: 'tone', label: 'Sfeer / toon', type: 'select', span: 'half', options: ['Stoer & energiek', 'Trots & ingetogen', 'Vrolijk & uptempo', 'Grappig & ad rem'] },
      { name: 'anecdotes', label: 'Verhaal van het bedrijf', type: 'textarea', required: true, span: 'full', placeholder: 'Wat maakt jullie bijzonder? Mooiste projecten, het team, de oprichter, typische uitspraken op de bouwplaats...', help: 'Deze details verwerken we in de songtekst.' },
      { name: 'email', label: 'Zakelijk e-mailadres', type: 'email', placeholder: 'naam@bedrijf.nl', required: true, span: 'full' },
    ],
    faq: [
      { question: 'Krijgen we de rechten om het zakelijk te gebruiken?', answer: 'Je ontvangt één vaste versie die je mag inzetten op je eigen kanalen (social media, website, bedrijfsfeest). Neem voor uitgebreidere commerciële licenties even contact op.' },
      { question: 'Kunnen jullie onze huisstijl/slogan verwerken?', answer: 'Ja, slogan en kernwaarden vormen vaak de haak (refrein) van het nummer.' },
      { question: 'Hoe snel is het klaar?', answer: 'Een eerste versie leveren we doorgaans binnen enkele werkdagen. Voor een specifieke deadline plannen we het samen in.' },
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

// Categorieën die in het /momenten-overzicht en de nav verschijnen.
export const consumerCategories = categories.filter((c) => c.variant !== 'b2b');
