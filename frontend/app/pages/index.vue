<script setup lang="ts">
import { requestCategories } from '~/data/categories';

const offer = useOffer();

useSeoMeta({
  title: 'Een persoonlijk nummer voor elk moment',
  ogTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  description:
    'Laat een persoonlijk nummer maken voor geslaagd, rijbewijs, eigen huis, teamlied, bouwbedrijf en meer. Met intake, lyrics en muziekprompt na betaling.',
  ogDescription:
    'Laat een persoonlijk nummer maken voor elk moment. Van intake tot lyrics en muziekprompt.',
});

useJsonLd([
  productSchema({
    name: 'Persoonlijk nummer',
    description:
      'Eén gepersonaliseerd nummer, samengesteld uit herbruikbare rijmblokken, persoonlijke context en een muziekprompt op maat.',
    price: offer.currentPrice.value,
    url: '/aanvraag',
  }),
]);

const heroStats = [
  { value: 'Intake op maat', label: 'per moment andere vragen' },
  { value: 'Lyrics + muziek', label: '2 stappen na betaling' },
  { value: '1 vaste versie', label: 'klaar om te delen' },
];

const pipeline = [
  { n: '01', title: 'Moment kiezen', text: 'Geslaagd, rijbewijs, teamlied, bouwbedrijf of een eigen gelegenheid.' },
  { n: '02', title: 'Briefing invullen', text: 'Namen, inside jokes, sfeer, stijl, wat erin moet en wat we vermijden.' },
  { n: '03', title: 'Lyrics engine', text: 'Templates, rijmende bouwstenen en jouw context vormen de definitieve tekst.' },
  { n: '04', title: 'Muziekprompt', text: 'Stem, energie, stijl en lyrics worden klaargezet voor de muziekprovider.' },
];

const valueProps = [
  {
    title: 'Niet kaal gegenereerd',
    body: 'We bouwen nummers uit vaste songstructuren, rijmende regels en persoonlijke details. Daardoor voelt het minder willekeurig en meer als een echt cadeau.',
  },
  {
    title: 'Formulieren die meedenken',
    body: 'Een bouwbedrijf krijgt andere vragen dan een verjaardag of teamlied. Zo krijg je input die bruikbaar is voor lyrics en muziek.',
  },
  {
    title: 'Duidelijke productieflow',
    body: 'Na betaling draait de backend twee stappen: eerst definitieve lyrics, daarna de muziekprompt of muziekprovider.',
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
  { q: 'Wordt de muziek al echt gegenereerd?', a: 'De flow staat klaar: na betaling worden definitieve lyrics en een muziekprompt opgeslagen. De huidige muziekprovider is nog een stub totdat de echte provider wordt gekoppeld.' },
  { q: 'Waarom zoveel context in het formulier?', a: 'Omdat goede persoonlijke nummers draaien om specifieke details: namen, plekken, zinnen, grappen, sfeer en wat juist niet genoemd mag worden.' },
  { q: 'Wat kost het?', a: offer.saleOn.value ? `Normaal ${offer.formattedRegular}, nu ${offer.formattedSale} per nummer.` : `${offer.formattedRegular} per nummer.` },
];
</script>

<template>
  <div class="overflow-hidden">
    <!-- HERO -->
    <section class="wide-container pt-6 pb-14 sm:pt-10 lg:pb-20">
      <div class="studio-shell min-h-[760px]">
        <img
          src="/hero-studio.png"
          alt=""
          class="absolute inset-0 h-full w-full object-cover opacity-[0.58]"
        />
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(16,26,22,0.96)_0%,rgba(16,26,22,0.82)_42%,rgba(16,26,22,0.28)_100%)]" />

        <div class="relative grid min-h-[760px] gap-10 p-5 sm:p-8 lg:grid-cols-[1fr_0.86fr] lg:p-12">
          <div class="flex max-w-3xl flex-col justify-center">
            <span v-reveal class="section-kicker" :style="{ background: 'rgba(255,255,255,0.1)', borderColor: 'rgba(255,255,255,0.16)', color: 'var(--accent-gold)' }">
              Van verhaal naar nummer
            </span>
            <h1 v-reveal class="font-display text-5xl font-semibold leading-[0.98] text-white sm:text-7xl lg:text-8xl">
              Laat jouw moment klinken als een echte release.
            </h1>
            <p v-reveal class="mt-7 max-w-2xl text-lg leading-8 text-white/[0.72]">
              Kies een moment, vul een slimme briefing in en laat de backend na betaling
              lyrics en een muziekprompt maken. Persoonlijk genoeg voor een cadeau,
              strak genoeg voor Spotify, socials of de kantine.
            </p>
            <div v-reveal class="mt-9 flex flex-col gap-3 sm:flex-row">
              <NuxtLink to="/aanvraag" class="stitch-button px-8 py-4 text-base">Start je nummer</NuxtLink>
              <NuxtLink to="/momenten" class="stitch-outline-button border-white/20 bg-white/10 px-8 py-4 text-base text-white hover:bg-white hover:text-[var(--color-ink)]">
                Bekijk momenten
              </NuxtLink>
            </div>

            <div v-reveal class="mt-10 grid gap-3 sm:grid-cols-3">
              <div v-for="stat in heroStats" :key="stat.value" class="glass-panel p-4">
                <p class="font-display text-2xl font-semibold text-white">{{ stat.value }}</p>
                <p class="mt-1 text-xs font-semibold leading-5 text-white/[0.58]">{{ stat.label }}</p>
              </div>
            </div>
          </div>

          <aside v-reveal class="flex items-end lg:items-center">
            <div class="w-full space-y-4">
              <div class="glass-panel p-5">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--accent-gold);">Live briefing</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold leading-tight text-white">Geslaagd-nummer voor Sophie</h2>
                  </div>
                  <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/[0.70]">Draft</span>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                  <div v-for="panel in studioPanels" :key="panel.label" class="border border-white/10 bg-white/[0.07] p-4" style="border-radius: 8px;">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-white/[0.42]">{{ panel.label }}</p>
                    <p class="mt-2 text-sm font-semibold text-white">{{ panel.value }}</p>
                  </div>
                </div>
              </div>

              <div class="glass-panel p-5">
                <div class="flex items-center justify-between gap-4 text-sm text-white/[0.68]">
                  <span>Lyrics</span>
                  <span>Muziekprompt</span>
                  <span>Release</span>
                </div>
                <div class="mt-4 h-2 overflow-hidden bg-white/10" style="border-radius: 999px;">
                  <div class="h-full w-[72%] accent-gradient" />
                </div>
                <div class="mt-5 grid gap-2">
                  <div class="h-2 w-11/12 bg-white/[0.12]" style="border-radius: 999px;" />
                  <div class="h-2 w-8/12 bg-white/[0.12]" style="border-radius: 999px;" />
                  <div class="h-2 w-10/12 bg-white/[0.12]" style="border-radius: 999px;" />
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <!-- VALUE -->
    <section class="site-container pb-14 sm:pb-20">
      <div class="grid gap-4 lg:grid-cols-3">
        <article v-for="item in valueProps" :key="item.title" v-reveal class="bento-card">
          <div class="mb-6 flex h-12 w-12 items-center justify-center bg-[var(--accent-soft)] font-display text-2xl font-semibold" style="border-radius: 8px; color: var(--accent-strong);">
            +
          </div>
          <h2 class="font-display text-3xl font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.title }}</h2>
          <p class="mt-4 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.body }}</p>
        </article>
      </div>
    </section>

    <!-- MOMENTS -->
    <section class="section-band py-16 sm:py-24">
      <div class="site-container">
        <div v-reveal class="mb-12 grid gap-6 lg:grid-cols-[0.82fr_1fr] lg:items-end">
          <div>
            <span class="section-kicker">Kies je startpunt</span>
            <h2 class="section-heading text-4xl sm:text-6xl">Voor cadeaus, teams en bedrijven</h2>
          </div>
          <p class="section-subtext max-w-2xl lg:justify-self-end">
            De site voelt minder generiek omdat elk moment een andere intake heeft.
            Een bouwbedrijf vraagt om projecten en slogan; een teamlied om clubcultuur;
            een verjaardag om karakter en inside jokes.
          </p>
        </div>

        <div v-reveal class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
        </div>
      </div>
    </section>

    <!-- PIPELINE -->
    <section class="py-16 sm:py-24" :style="{ background: 'var(--color-bg-contrast)' }">
      <div class="site-container">
        <div v-reveal class="mb-12 max-w-3xl">
          <span class="section-kicker" :style="{ background: 'rgba(255,255,255,0.08)', borderColor: 'rgba(255,255,255,0.14)', color: 'var(--accent-gold)' }">Productieflow</span>
          <h2 class="font-display text-4xl font-semibold leading-[1.02] text-white sm:text-6xl">
            Na betaling draait er meer dan alleen een bedanktpagina.
          </h2>
          <p class="mt-5 text-base leading-7 text-white/[0.64]">
            De flow is opgezet als moderne productiepipeline. Vandaag met stub-provider,
            straks met echte muziekprovider zonder de funnel opnieuw te bouwen.
          </p>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
          <article v-for="step in pipeline" :key="step.n" v-reveal class="glass-panel p-6">
            <p class="font-display text-5xl font-semibold" style="color: var(--accent-gold);">{{ step.n }}</p>
            <h3 class="mt-6 font-display text-2xl font-semibold text-white">{{ step.title }}</h3>
            <p class="mt-3 text-sm leading-7 text-white/[0.60]">{{ step.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- PROOF / USE CASES -->
    <section class="site-container py-16 sm:py-24">
      <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div v-reveal>
          <span class="section-kicker">Vol genoeg om te overtuigen</span>
          <h2 class="section-heading text-4xl sm:text-6xl">Niet alleen een formulier, maar een productervaring</h2>
          <p class="section-subtext mt-5 max-w-xl">
            Bezoekers zien direct wat er gebeurt, wat ze moeten invullen en waarom
            dat betere lyrics oplevert. Dat maakt de site voller, duidelijker en betrouwbaarder.
          </p>
          <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <NuxtLink to="/aanvraag" class="stitch-button px-8 py-3.5">Maak mijn nummer</NuxtLink>
            <NuxtLink to="/zakelijk/bouwbedrijven" class="stitch-outline-button px-8 py-3.5">Zakelijk nummer</NuxtLink>
          </div>
        </div>

        <div class="grid gap-4">
          <article v-for="item in proof" :key="item.title" v-reveal class="bento-card grid gap-3 sm:grid-cols-[0.28fr_1fr] sm:items-center">
            <div class="font-display text-5xl font-semibold" :style="{ color: 'var(--accent-strong)' }">♪</div>
            <div>
              <h3 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.title }}</h3>
              <p class="mt-2 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.text }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- PRICE CTA -->
    <section class="wide-container pb-16 sm:pb-24">
      <div v-reveal class="studio-shell grid gap-8 p-6 sm:p-10 lg:grid-cols-[1fr_0.58fr] lg:items-center">
        <div>
          <span class="section-kicker" :style="{ background: 'rgba(255,255,255,0.08)', borderColor: 'rgba(255,255,255,0.14)', color: 'var(--accent-gold)' }">Eén duidelijke prijs</span>
          <h2 class="font-display text-4xl font-semibold leading-[1.02] text-white sm:text-6xl">
            Start met context. Eindig met een nummer dat je durft te delen.
          </h2>
          <p class="mt-5 max-w-2xl text-base leading-7 text-white/[0.66]">
            Geen lege generator, maar een intake en productieflow die jouw verhaal omzet
            naar lyrics en een muziekprompt.
          </p>
        </div>
        <div class="glass-panel p-6 text-center">
          <p class="text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--accent-gold);">Vanaf</p>
          <div class="mt-5 flex justify-center">
            <OfferBadge size="lg" />
          </div>
          <NuxtLink to="/aanvraag" class="stitch-button mt-7 w-full px-8 py-4">Aanvraag starten</NuxtLink>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="site-container pb-20">
      <div class="mx-auto max-w-3xl">
        <div v-reveal class="mb-8 text-center">
          <span class="section-kicker">Goed om te weten</span>
          <h2 class="section-heading text-3xl sm:text-5xl">Veelgestelde vragen</h2>
        </div>
        <div class="space-y-3">
          <details v-for="item in faqPreview" :key="item.q" v-reveal class="rich-card p-6">
            <summary class="cursor-pointer list-none font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.q }}</summary>
            <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.a }}</p>
          </details>
        </div>
      </div>
    </section>
  </div>
</template>
