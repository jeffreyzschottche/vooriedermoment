<script setup lang="ts">
import { categoryPath, requestCategories } from '~/data/categories';
import { reviews } from '~/data/reviews';

const offer = useOffer();
const spotifyUrl = SPOTIFY_ARTIST_URL;

// Lijn-iconen (24×24, stroke) voor de voordelen en doelgroepen.
const icons: Record<string, string> = {
  heart: 'M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z',
  pen: 'M4 20h4L19 9l-4-4L4 16v4Zm9-13 4 4',
  wave: 'M3 12h2m4-5v10m4-13v16m4-11v6m4-3h-2',
  home: 'M4 11 12 4l8 7v9h-5v-6H9v6H4v-9Z',
  team: 'M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM2 20c0-3 3-5 6-5s6 2 6 5m0-5c3 0 8 1 8 5',
  building: 'M4 21V5l8-2v18m0-12 8 2v10M8 9h0m0 4h0m0 4h0m8-2h0m0 4h0',
};

useSeoMeta({
  title: 'Een persoonlijk nummer voor elk moment',
  ogTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  description:
    'Laat voor € 4,99 een persoonlijk Nederlandstalig nummer maken voor een verjaardag, rijbewijs, geboorte, team of bedrijf. Je songtekst direct in je mail, jouw favoriet op Spotify.',
  ogDescription:
    'Vertel jouw verhaal, ontvang direct je persoonlijke songtekst en luister straks naar je eigen nummer op Spotify en YouTube Music.',
  ogUrl: 'https://vooriedermoment.nl/',
  ogImage: 'https://vooriedermoment.nl/hero-studio.png',
  twitterTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  twitterDescription: 'Laat een persoonlijk nummer maken voor elk moment.',
});


const heroStats = [
  { display: '€ 4,99', label: 'alles inbegrepen' },
  { display: 'Direct', label: 'je songtekst in je mail' },
  { display: 'Op Spotify', label: 'door ons geregeld' },
];

const pipeline = [
  { n: '01', title: 'Moment kiezen', text: 'Geslaagd, rijbewijs, teamlied, bouwbedrijf of een eigen gelegenheid.' },
  { n: '02', title: 'Vertel jouw verhaal', text: 'Deel namen, herinneringen en inside jokes. Direct na betaling staat je persoonlijke songtekst in je mail.' },
  { n: '03', title: 'Kies uit de opties', text: 'Binnen 48 uur ontvang je verschillende korte voorproefjes in je inbox.' },
  { n: '04', title: 'Wij regelen de release', text: 'Wij maken jouw favoriet binnen 48 uur compleet en bieden hem aan bij Spotify en YouTube Music.' },
];

const valueProps = [
  {
    icon: 'heart',
    title: 'Jouw verhaal staat centraal',
    body: 'Geen standaardtekst met alleen een andere naam. We verwerken echte herinneringen, uitspraken en details die alleen jullie herkennen.',
  },
  {
    icon: 'pen',
    title: 'Vragen die bij het moment passen',
    body: 'Een bouwbedrijf vraagt om andere details dan een verjaardag of teamlied. Daarom sluit iedere aanvraag aan op de gelegenheid.',
  },
  {
    icon: 'wave',
    title: 'Van sample naar streaming',
    body: 'Kies het voorproefje dat het beste voelt. Wij maken die versie compleet en regelen de release op Spotify en YouTube Music.',
  },
];

const proof = [
  { icon: 'home', title: 'Voor families', text: 'Een cadeau dat niet in de kast verdwijnt, maar op het feest zelf een moment maakt.' },
  { icon: 'team', title: 'Voor teams', text: 'Een meezinger voor kleedkamer, kantine, kampioenschap of seizoensafsluiting.' },
  { icon: 'building', title: 'Voor bedrijven', text: 'Een herkenbaar bedrijfsnummer met naam, slogan, projecten en teamcultuur.' },
];

const faqPreview = [
  { q: 'Hoe werkt het precies?', a: 'Je kiest een moment en vult stap voor stap ons formulier in met namen, herinneringen en de gewenste sfeer. Direct na betaling ontvang je je persoonlijke songtekst per e-mail; daarna sturen we je verschillende muzikale opties. Jij kiest je favoriet; wij maken het nummer compleet en publiceren het voor je op Spotify en YouTube Music.' },
  { q: 'Wanneer krijg ik de songtekst?', a: 'Meteen na je betaling. Je ontvangt een bevestiging met de volledige songtekst, geschreven op basis van jouw verhaal. Zo weet je direct wat er straks gezongen wordt.' },
  { q: 'Moet ik zelf iets regelen voor Spotify?', a: 'Nee. Wij verzorgen de volledige publicatie op Spotify en YouTube Music. Je hebt geen artiestenaccount of technische kennis nodig. Zodra jouw nummer live staat, ontvang je de directe luisterlinks per e-mail.' },
  { q: 'Hoe snel staat mijn nummer online?', a: 'Binnen 48 uur na betaling ontvang je de muzikale opties. Na jouw keuze maken we de favoriet binnen 48 uur compleet en bieden we hem aan bij Spotify en YouTube Music. Wanneer het nummer live staat, hangt af van de goedkeuring door de platforms.' },
  { q: 'Kan ik mijn nummer later opnieuw beluisteren?', a: 'Ja. Jouw nummer wordt als een echte release gepubliceerd. Voeg het toe aan je Spotify- of YouTube Music-bibliotheek en luister er zo vaak naar als je wilt.' },
  { q: 'Kan ik het nummer met anderen delen?', a: 'Zeker. Deel de luisterlink eenvoudig via WhatsApp, e-mail of social media met familie, vrienden, collega’s of het hele team.' },
  { q: 'Wat als mijn moment er niet tussen staat?', a: 'Kies dan “Een ander moment”. Van pensioen, jubileum en afscheid tot een bedankje of een herinnering die alleen voor jullie betekenis heeft: jij vertelt het verhaal, wij maken het nummer.' },
];

useJsonLd([
  productSchema({
    name: 'Persoonlijk nummer',
    description:
      'Eén persoonlijk nummer op basis van jouw verhaal, met keuze uit verschillende muzikale opties en publicatie op Spotify en YouTube Music.',
    price: offer.currentPrice.value,
    url: '/aanvraag',
    reviews,
  }),
  serviceSchema({
    price: offer.currentPrice.value,
    moments: requestCategories.map((c) => ({ name: c.title, path: categoryPath(c), description: c.seoDescription })),
  }),
  musicGroupSchema,
  faqSchema(faqPreview.map((item) => ({ question: item.q, answer: item.a }))),
]);
</script>

<template>
  <div class="overflow-x-clip">
    <!-- HERO -->
    <section class="hero-fresh relative">
      <div class="hero-blob hero-blob-a" aria-hidden="true" />
      <div class="hero-blob hero-blob-b" aria-hidden="true" />

      <div class="hero-inner relative grid gap-14 pb-20 pt-12 sm:pt-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-10 lg:pb-28 lg:pt-20">
        <div class="max-w-2xl">
          <a
            v-hero-reveal
            :href="spotifyUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-2 rounded-full border bg-white py-1.5 pl-1.5 pr-4 text-sm font-semibold shadow-sm transition hover:border-[#1ed760]"
            :style="{ borderColor: 'var(--color-line)', color: 'var(--color-ink)' }"
          >
            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#121412]">
              <SpotifyLogo class="h-4 w-4" />
            </span>
            Onze nummers staan op Spotify
            <span aria-hidden="true" :style="{ color: 'var(--color-ink-faint)' }">→</span>
          </a>

          <h1
            v-hero-reveal
            data-hero-delay="0.1"
            class="mt-7 font-display text-[3.4rem] font-bold leading-[0.92] tracking-[-0.045em] sm:text-7xl lg:text-[5.6rem]"
            :style="{ color: 'var(--color-ink)' }"
          >
            Jouw moment.<br />
            <span class="hero-highlight">Jouw nummer.</span>
            <Shape3d kind="ring" :size="52" :delay="2" class="ml-2 hidden align-middle sm:inline-block" />
          </h1>

          <p
            v-hero-reveal
            data-hero-delay="0.2"
            class="mt-7 max-w-xl text-lg leading-relaxed sm:text-xl"
            :style="{ color: 'var(--color-ink-soft)' }"
          >
            Vertel wat dit moment bijzonder maakt. Je songtekst staat direct na betaling in je mail,
            daarna kies je uit verschillende voorproefjes en zetten wij jouw favoriet op Spotify.
          </p>

          <div v-hero-reveal data-hero-delay="0.3" class="mt-9 flex flex-col gap-3 sm:flex-row">
            <NuxtLink to="/aanvraag" class="stitch-button px-8 py-4 text-base">
              Maak je nummer <span aria-hidden="true">→</span>
            </NuxtLink>
            <NuxtLink to="/momenten" class="stitch-outline-button px-8 py-4 text-base">
              Bekijk momenten
            </NuxtLink>
          </div>

          <ul v-hero-reveal data-hero-delay="0.4" class="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold" :style="{ color: 'var(--color-ink-soft)' }">
            <li v-for="stat in heroStats" :key="stat.label" class="inline-flex items-center gap-2">
              <span class="flex h-5 w-5 items-center justify-center rounded-full text-[11px]" :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }" aria-hidden="true">✓</span>
              <span><strong :style="{ color: 'var(--color-ink)' }">{{ stat.display }}</strong> {{ stat.label }}</span>
            </li>
          </ul>
        </div>

        <!-- Visual: studio-foto met wisselende songtekst en Spotify-speler -->
        <div v-hero-reveal data-hero-delay="0.35" class="relative mx-auto w-full max-w-[540px] lg:mr-0">
          <Shape3d kind="note" :size="110" class="absolute -right-4 -top-10 z-10 sm:-right-12" />
          <Shape3d kind="sphere" :size="70" :delay="3" class="absolute -left-14 bottom-24 z-10 hidden sm:block" />
          <Shape3d kind="star" :size="54" :delay="5" class="absolute right-10 top-1/2 z-10 hidden lg:block" />
          <div v-tilt="5" class="relative aspect-[4/5] overflow-hidden rounded-[32px] shadow-[0_40px_80px_-30px_rgba(18,20,18,0.45)] sm:aspect-[5/5.4]">
            <img src="/hero-studio.png" alt="" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-[rgba(13,21,18,0.75)] via-[rgba(13,21,18,0.1)] to-transparent" />
          </div>

          <HeroDemoPlayer />
        </div>
      </div>
    </section>

    <!-- COVER-MUUR -->
    <section class="pb-6 pt-4">
      <CoverWall />
    </section>

    <!-- VALUE PROPS -->
    <section class="py-20 sm:py-28">
      <div class="site-container">
        <div v-reveal-stagger data-stagger="0.1" class="grid gap-6 lg:grid-cols-3">
          <article v-for="item in valueProps" :key="item.title" v-tilt="6" class="bento-card">
            <div class="bento-card-icon mb-6">
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path :d="icons[item.icon]" /></svg>
            </div>
            <h2 class="font-display text-2xl font-semibold sm:text-3xl" :style="{ color: 'var(--color-ink)' }">
              {{ item.title }}
            </h2>
            <p class="mt-4 text-base leading-7" :style="{ color: 'var(--color-ink-soft)' }">
              {{ item.body }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- MOMENTS -->
    <section class="section-band py-20 sm:py-28">
      <div class="site-container">
        <div v-reveal class="mb-14 grid gap-8 lg:grid-cols-[0.85fr_1fr] lg:items-end">
          <div>
            <span class="section-kicker">Kies je startpunt</span>
            <h2 class="section-heading text-4xl sm:text-5xl lg:text-6xl">
              Voor bekende én totaal eigen momenten
            </h2>
          </div>
          <p class="section-subtext max-w-2xl lg:justify-self-end">
            Van geboorte en verjaardag tot jubileum, pensioen, team, bedrijf of een moment
            dat alleen voor jullie betekenis heeft. Staat jouw gelegenheid er niet tussen?
            Kies dan ‘Een ander moment’ en vertel ons wat er gevierd, bedankt of herinnerd mag worden.
          </p>
        </div>

        <div v-reveal-stagger data-stagger="0.08" class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
          <MomentCard
            v-for="c in requestCategories"
            :key="c.slug"
            :category="c"
            other-moment-span="remainder"
          />
        </div>
      </div>
    </section>

    <!-- SPOTIFY: onze nummers (artiest-embed, werkt zichzelf bij) -->
    <section class="py-20 sm:py-28">
      <div v-reveal class="site-container">
        <PlaylistShowcase
          embed="https://open.spotify.com/embed/artist/5iXRrtThUvD2eKB4hbsGXo?utm_source=generator"
          kicker="Nu op Spotify"
          title="Luister naar onze nummers"
          text="Van rijbewijs tot Vaderdag: deze nummers staan al op Spotify. Zo klinkt een persoonlijk nummer van Voor Ieder Moment."
          :chips="['Echte releases', 'Spotify & YouTube Music', 'Voor ieder moment']"
          request-href="/aanvraag"
        />
      </div>
    </section>

    <!-- REVIEWS (alleen zichtbaar met echte reviews in data/reviews.ts) -->
    <ReviewsSection />

    <!-- PIPELINE -->
    <section class="py-6 sm:py-10">
      <div class="edge-container">
      <div class="studio-shell py-20 sm:py-24">
      <Shape3d kind="disc" :size="220" class="absolute -right-16 -top-16 hidden opacity-90 md:block" />
      <Shape3d kind="star" :size="44" :delay="4" class="absolute bottom-10 left-8 hidden md:block" />
      <div class="site-container relative">
        <div v-reveal class="mb-14 max-w-3xl">
          <span
            class="section-kicker"
            style="color: var(--accent-gold);"
          >
            Zo werkt het
          </span>
          <h2 class="font-display text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
            Van jouw verhaal naar een echte release
          </h2>
          <p class="mt-6 text-lg leading-relaxed text-white/60">
            Jij vertelt wat het nummer persoonlijk maakt en kiest uit verschillende muzikale opties.
            Wij werken jouw favoriet uit en publiceren hem voor je op Spotify en YouTube Music.
          </p>
        </div>

        <div v-reveal-stagger data-stagger="0.12" class="grid gap-5 lg:grid-cols-4">
          <article v-for="step in pipeline" :key="step.n" class="glass-panel p-7 transition hover:bg-white/10">
            <p class="inline-flex h-10 w-10 items-center justify-center rounded-full font-display text-sm font-bold" style="background: var(--accent); color: var(--accent-ink);">
              {{ step.n }}
            </p>
            <h3 class="mt-6 font-display text-xl font-semibold text-white lg:text-2xl">
              {{ step.title }}
            </h3>
            <p class="mt-3 text-sm leading-7 text-white/55">
              {{ step.text }}
            </p>
          </article>
        </div>
      </div>
      </div>
      </div>
    </section>

    <!-- PROOF / USE CASES -->
    <section class="py-20 sm:py-28">
      <div class="site-container">
        <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
          <div v-reveal>
            <span class="section-kicker">Voor iedereen</span>
            <h2 class="section-heading text-4xl sm:text-5xl lg:text-6xl">
              Een cadeau dat niemand ziet aankomen
            </h2>
            <p class="section-subtext mt-6 max-w-xl">
              Persoonlijk genoeg voor kippenvel, verrassend genoeg om nog lang over na te praten.
              Voor thuis, in de kleedkamer of op de werkvloer.
            </p>
            <div class="mt-10 flex flex-col gap-4 sm:flex-row">
              <NuxtLink to="/aanvraag" class="stitch-button px-8 py-4">
                Maak mijn nummer
              </NuxtLink>
              <NuxtLink to="/momenten/bouwbedrijven" class="stitch-outline-button px-8 py-4">
                Zakelijk nummer
              </NuxtLink>
            </div>
          </div>

          <div v-reveal-stagger data-stagger="0.1" class="space-y-5">
            <article
              v-for="item in proof"
              :key="item.title"
              v-tilt="4"
              class="bento-card flex items-start gap-5"
            >
              <div class="bento-card-icon shrink-0">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path :d="icons[item.icon]" /></svg>
              </div>
              <div>
                <h3 class="font-display text-xl font-semibold sm:text-2xl" :style="{ color: 'var(--color-ink)' }">
                  {{ item.title }}
                </h3>
                <p class="mt-2 text-base leading-7" :style="{ color: 'var(--color-ink-soft)' }">
                  {{ item.text }}
                </p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- PRICE CTA -->
    <section class="py-8 sm:py-12">
      <div class="edge-container">
        <div v-reveal class="studio-shell grid gap-10 p-8 sm:p-12 lg:grid-cols-[1fr_0.5fr] lg:items-center">
          <Shape3d kind="sphere" :size="80" :delay="2" class="absolute bottom-6 left-[52%] hidden lg:block" />
          <Shape3d kind="note" :size="80" :delay="6" class="absolute top-6 right-[32%] hidden lg:block" />
          <div class="relative">
            <span
              class="section-kicker"
              style="color: var(--accent-gold);"
            >
              Eén duidelijke prijs
            </span>
            <h2 class="font-display text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
              Jouw verhaal. Straks overal te beluisteren.
            </h2>
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/60 sm:text-lg">
              Voor € 4,99 kies je uit verschillende muzikale opties. Wij maken jouw favoriet
              compleet en publiceren hem voor je, zodat je hem steeds opnieuw kunt beluisteren en delen.
            </p>
          </div>
          <div v-tilt="6" class="glass-panel relative z-10 p-7 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--accent-gold);">Complete prijs</p>
            <div class="mt-5 flex justify-center">
              <OfferBadge size="lg" />
            </div>
            <NuxtLink to="/aanvraag" class="stitch-button mt-8 w-full py-4 text-base">
              Start aanvraag
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 sm:py-28">
      <div class="site-container">
        <div class="mx-auto max-w-3xl">
          <div v-reveal class="mb-10 text-center">
            <span class="section-kicker">Goed om te weten</span>
            <h2 class="section-heading text-3xl sm:text-4xl lg:text-5xl">Veelgestelde vragen</h2>
          </div>
          <div v-reveal-stagger data-stagger="0.08" class="space-y-4">
            <details v-for="item in faqPreview" :key="item.q" class="rich-card p-7">
              <summary class="cursor-pointer list-none font-semibold" :style="{ color: 'var(--color-ink)' }">
                {{ item.q }}
              </summary>
              <p class="mt-4 text-base leading-7" :style="{ color: 'var(--color-ink-soft)' }">
                {{ item.a }}
              </p>
            </details>
          </div>
          <div class="mt-8 text-center">
            <NuxtLink to="/faq-contact" class="stitch-outline-button px-8 py-3.5">
              Bekijk alle vragen
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero-fresh {
  overflow: hidden;
}
.hero-blob {
  position: absolute;
  border-radius: 999px;
  filter: blur(80px);
  pointer-events: none;
}
.hero-blob-a {
  width: 520px;
  height: 520px;
  right: -120px;
  top: -160px;
  background: color-mix(in srgb, var(--accent) 22%, transparent);
}
.hero-blob-b {
  width: 420px;
  height: 420px;
  left: -160px;
  bottom: -120px;
  background: color-mix(in srgb, var(--accent-gold) 22%, transparent);
}
.hero-highlight {
  color: var(--accent);
}
</style>
