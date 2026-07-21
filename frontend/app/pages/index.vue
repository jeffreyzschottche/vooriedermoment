<script setup lang="ts">
import { requestCategories } from '~/data/categories';

const offer = useOffer();

useSeoMeta({
  title: 'Een persoonlijk nummer voor elk moment',
  ogTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  description:
    'Laat voor € 9,99 een persoonlijk nummer maken voor een verjaardag, rijbewijs, nieuw huis, team, bedrijf en meer. Inclusief 4 samples en release op Spotify en Apple Music.',
  ogDescription:
    'Vertel jouw verhaal, kies uit 4 samples en luister naar je eigen nummer op Spotify en Apple Music.',
  ogUrl: 'https://vooriedermoment.nl/',
  ogImage: 'https://vooriedermoment.nl/hero-studio.png',
  twitterTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  twitterDescription: 'Laat een persoonlijk nummer maken voor elk moment.',
});

useJsonLd([
  productSchema({
    name: 'Persoonlijk nummer',
    description:
      'Eén persoonlijk nummer op basis van jouw verhaal, inclusief vier samples en release op Spotify en Apple Music.',
    price: offer.currentPrice.value,
    url: '/aanvraag',
  }),
]);

const heroStats = [
  { value: '9', prefix: '€ ', suffix: ',99', label: 'één complete prijs' },
  { value: '4', prefix: '', suffix: ' samples', label: 'om uit te kiezen' },
  { value: '1', prefix: '', suffix: ' nummer', label: 'op Spotify & Apple Music' },
];

const pipeline = [
  { n: '01', title: 'Moment kiezen', text: 'Geslaagd, rijbewijs, teamlied, bouwbedrijf of een eigen gelegenheid.' },
  { n: '02', title: 'Vertel jouw verhaal', text: 'Deel namen, herinneringen, inside jokes en de sfeer die bij het moment past.' },
  { n: '03', title: 'Luister naar 4 samples', text: 'Binnen 24–72 uur ontvang je vier unieke voorproefjes in je inbox.' },
  { n: '04', title: 'Kies jouw favoriet', text: 'Wij maken hem af en publiceren hem binnen 24–72 uur op Spotify en Apple Music.' },
];

const valueProps = [
  {
    icon: '🎵',
    title: 'Jouw verhaal staat centraal',
    body: 'Geen standaardtekst met alleen een andere naam. We verwerken echte herinneringen, uitspraken en details die alleen jullie herkennen.',
  },
  {
    icon: '📝',
    title: 'Vragen die bij het moment passen',
    body: 'Een bouwbedrijf vraagt om andere details dan een verjaardag of teamlied. Daarom sluit iedere aanvraag aan op de gelegenheid.',
  },
  {
    icon: '🚀',
    title: 'Van sample naar streaming',
    body: 'Kies het voorproefje dat het beste voelt. Wij maken die versie compleet en regelen de release op Spotify en Apple Music.',
  },
];

const studioPanels = [
  { label: 'Naam', value: 'Sophie' },
  { label: 'Moment', value: 'Geslaagd' },
  { label: 'Stijl', value: 'Feest / pop' },
  { label: 'Must-have', value: 'Vlag, docent, klasgrap' },
];

const proof = [
  { title: 'Voor families', text: 'Een cadeau dat niet in de kast verdwijnt, maar op het feest zelf een moment maakt.' },
  { title: 'Voor teams', text: 'Een meezinger voor kleedkamer, kantine, kampioenschap of seizoensafsluiting.' },
  { title: 'Voor bedrijven', text: 'Een herkenbaar bedrijfsnummer met naam, slogan, projecten en teamcultuur.' },
];

const faqPreview = [
  { q: 'Hoe werkt het precies?', a: 'Na betaling ontvang je binnen 24–72 uur vier unieke samples van 15 seconden. Zodra je jouw favoriet hebt gekozen, staat de complete versie binnen 24–72 uur op Spotify en Apple Music.' },
  { q: 'Waarom zoveel context in het formulier?', a: 'Omdat goede persoonlijke nummers draaien om specifieke details: namen, plekken, zinnen, grappen en sfeer. Hoe concreter, hoe unieker jouw nummer.' },
  { q: 'Wat kost het?', a: offer.hasDiscount.value ? `Normaal ${offer.formattedRegular}, nu ${offer.formattedSale}. Inclusief vier samples en release op Spotify en Apple Music.` : `${offer.formattedRegular}. Inclusief vier samples en release op Spotify en Apple Music.` },
];
</script>

<template>
  <div class="overflow-hidden">
    <!-- HERO - Full Screen -->
    <section class="hero-section studio-shell-full">
      <!-- Background image with parallax -->
      <div class="absolute inset-0">
        <img
          v-parallax
          data-parallax-speed="0.2"
          src="/hero-studio.png"
          alt=""
          class="h-[120%] w-full object-cover opacity-40"
        />
      </div>
      <!-- Gradient overlays -->
      <div class="absolute inset-0 bg-gradient-to-r from-[rgba(13,21,18,0.97)] via-[rgba(13,21,18,0.85)] to-[rgba(13,21,18,0.4)]" />
      <div class="absolute inset-0 bg-gradient-to-t from-[rgba(13,21,18,0.5)] via-transparent to-transparent" />

      <div class="hero-inner relative flex min-h-[calc(100vh-4.5rem)] flex-col justify-center py-16 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
          <!-- Left content -->
          <div class="max-w-2xl">
            <span
              v-hero-reveal
              class="section-kicker"
              style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); color: var(--accent-gold);"
            >
              Van jouw verhaal naar muziek
            </span>

            <h1
              v-hero-reveal
              data-hero-delay="0.1"
              class="font-display text-5xl font-semibold leading-[0.95] tracking-tight text-white sm:text-6xl lg:text-7xl xl:text-8xl"
            >
              Jouw moment.
              <span class="gradient-text">Jouw nummer.</span>
            </h1>

            <p
              v-hero-reveal
              data-hero-delay="0.2"
              class="mt-8 max-w-xl text-lg leading-relaxed text-white/70 sm:text-xl"
            >
              Vertel wat dit moment bijzonder maakt en ontvang vier unieke samples.
              Kies jouw favoriet; wij maken hem af voor Spotify en Apple Music.
            </p>

            <div
              v-hero-reveal
              data-hero-delay="0.3"
              class="mt-10 flex flex-col gap-4 sm:flex-row"
            >
              <NuxtLink to="/aanvraag" class="stitch-button px-10 py-5 text-base">
                Start je nummer
              </NuxtLink>
              <NuxtLink
                to="/momenten"
                class="stitch-outline-button border-white/20 bg-white/5 px-10 py-5 text-base text-white hover:bg-white hover:text-[var(--color-ink)]"
              >
                Bekijk momenten
              </NuxtLink>
            </div>

            <!-- Stats -->
            <div
              v-hero-reveal
              data-hero-delay="0.4"
              class="mt-14 grid gap-4 sm:grid-cols-3"
            >
              <div
                v-for="(stat, i) in heroStats"
                :key="stat.label"
                class="glass-panel p-5"
              >
                <p class="font-display text-3xl font-semibold text-white">
                  <span
                    v-counter
                    :data-counter-target="stat.value"
                    :data-counter-prefix="stat.prefix"
                    :data-counter-suffix="stat.suffix"
                  >{{ stat.prefix }}{{ stat.value }}{{ stat.suffix }}</span>
                </p>
                <p class="mt-1.5 text-sm font-medium text-white/50">{{ stat.label }}</p>
              </div>
            </div>
          </div>

          <!-- Right: Live briefing preview -->
          <aside v-hero-reveal data-hero-delay="0.5" class="hidden lg:block">
            <div class="space-y-5">
              <div class="glass-panel p-6">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--accent-gold);">Live briefing</p>
                    <h2 class="mt-3 font-display text-2xl font-semibold leading-tight text-white lg:text-3xl">
                      Geslaagd-nummer voor Sophie
                    </h2>
                  </div>
                  <span class="rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-white/60">Demo</span>
                </div>
                <div v-reveal-stagger data-stagger="0.06" class="mt-6 grid gap-3 sm:grid-cols-2">
                  <div
                    v-for="panel in studioPanels"
                    :key="panel.label"
                    class="rounded-xl border border-white/10 bg-white/5 p-4"
                  >
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/40">{{ panel.label }}</p>
                    <p class="mt-2 text-sm font-semibold text-white">{{ panel.value }}</p>
                  </div>
                </div>
              </div>

              <div class="glass-panel p-6">
                <div class="flex items-center justify-between gap-4 text-sm text-white/60">
                  <span>Samples</span>
                  <span>Keuze</span>
                  <span>Spotify</span>
                </div>
                <div class="progress-bar mt-4">
                  <div class="progress-bar-fill" style="width: 66%;" />
                </div>
                <div class="mt-5 space-y-2">
                  <div class="h-2.5 w-11/12 rounded-full bg-white/10" />
                  <div class="h-2.5 w-8/12 rounded-full bg-white/10" />
                  <div class="h-2.5 w-10/12 rounded-full bg-white/10" />
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
        <div class="flex flex-col items-center gap-2 text-white/40">
          <span class="text-xs font-medium uppercase tracking-widest">Scroll</span>
          <div class="h-10 w-5 rounded-full border border-white/20 p-1">
            <div class="h-2 w-full animate-bounce rounded-full bg-white/40" />
          </div>
        </div>
      </div>
    </section>

    <!-- VALUE PROPS -->
    <section class="py-20 sm:py-28">
      <div class="site-container">
        <div v-reveal-stagger data-stagger="0.1" class="grid gap-6 lg:grid-cols-3">
          <article v-for="item in valueProps" :key="item.title" class="bento-card">
            <div class="bento-card-icon mb-6">
              {{ item.icon }}
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
              Voor cadeaus, teams en bedrijven
            </h2>
          </div>
          <p class="section-subtext max-w-2xl lg:justify-self-end">
            Elk moment verdient andere vragen. Bij een bouwbedrijf draait het om projecten
            en een slogan, bij een teamlied om clubcultuur en bij een verjaardag om karakter en inside jokes.
          </p>
        </div>

        <div v-reveal-stagger data-stagger="0.08" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
        </div>
      </div>
    </section>

    <!-- PIPELINE -->
    <section class="studio-shell-full py-20 sm:py-28">
      <div class="site-container">
        <div v-reveal class="mb-14 max-w-3xl">
          <span
            class="section-kicker"
            style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: var(--accent-gold);"
          >
            Zo werkt het
          </span>
          <h2 class="font-display text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
            Van jouw verhaal naar een echte release
          </h2>
          <p class="mt-6 text-lg leading-relaxed text-white/60">
            Jij levert de details, wij maken vier muzikale richtingen. Na jouw keuze
            werken we de favoriet uit en regelen we de release.
          </p>
        </div>

        <div v-reveal-stagger data-stagger="0.12" class="grid gap-5 lg:grid-cols-4">
          <article v-for="step in pipeline" :key="step.n" class="glass-panel p-7">
            <p class="font-display text-5xl font-semibold" style="color: var(--accent-gold);">
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
              class="bento-card flex items-start gap-5"
            >
              <div class="bento-card-icon shrink-0 text-3xl">♪</div>
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
          <div>
            <span
              class="section-kicker"
              style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: var(--accent-gold);"
            >
              Eén duidelijke prijs
            </span>
            <h2 class="font-display text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
              Jouw verhaal. Straks overal te beluisteren.
            </h2>
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/60 sm:text-lg">
              Voor € 9,99 krijg je vier unieke samples, één complete versie en de release
              op Spotify en Apple Music.
            </p>
          </div>
          <div class="glass-panel p-7 text-center">
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
        </div>
      </div>
    </section>
  </div>
</template>
