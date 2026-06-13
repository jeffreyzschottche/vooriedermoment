<script setup lang="ts">
import { consumerCategories } from '~/data/categories';

const offer = useOffer();

useSeoMeta({
  title: 'Een persoonlijk nummer voor elk moment',
  ogTitle: 'Voor Ieder Moment — een persoonlijk nummer voor elk moment',
  description:
    'Een persoonlijk nummer voor elk moment: geslaagd, rijbewijs, eigen huis, vaderdag en meer. Snel, betaalbaar en overal te beluisteren op Spotify en Apple Music.',
  ogDescription:
    'Een persoonlijk nummer voor elk moment. Snel, betaalbaar en overal te beluisteren op Spotify en Apple Music.',
});

useJsonLd([
  productSchema({
    name: 'Persoonlijk nummer',
    description:
      'Eén gepersonaliseerd nummer, samengesteld uit kant-en-klare bouwstenen en met AI op maat gemaakt. Overal te beluisteren op Spotify en Apple Music.',
    price: offer.currentPrice.value,
    url: '/aanvraag',
  }),
]);

const steps = [
  { n: '01', t: 'Kies je moment', d: 'Geslaagd, rijbewijs, eigen huis, verjaardag, een clublied… of een eigen gelegenheid.' },
  { n: '02', t: 'Vertel je verhaal', d: 'Namen, anekdotes en inside jokes. Hoe concreter, hoe persoonlijker.' },
  { n: '03', t: 'Wij maken je nummer', d: 'We combineren kant-en-klare bouwstenen met AI-tekst op maat tot één afgewerkt nummer.' },
  { n: '04', t: 'Overal te delen', d: 'Je krijgt één vaste versie, klaar voor Spotify, Apple Music en social media.' },
];

const heroStats = [
  { value: '1 nummer', label: 'helemaal persoonlijk gemaakt' },
  { value: 'Snel klaar', label: 'zonder lang creatief traject' },
  { value: 'Overal deelbaar', label: 'voor Spotify, Apple Music en social' },
];

const valueProps = [
  {
    title: 'Een cadeau dat blijft hangen',
    body: 'Geen standaard berichtje of bos bloemen, maar een lied dat precies bij de persoon en het moment past.',
  },
  {
    title: 'Persoonlijk, maar betaalbaar',
    body: 'We gebruiken slimme bouwstenen en AI-personalisatie. Daardoor voelt het eigen, zonder studio-prijskaartje.',
  },
  {
    title: 'Gemaakt om te delen',
    body: 'Je krijgt één vaste versie met een duidelijke hook, zodat je hem makkelijk draait op een feest of deelt in de groepsapp.',
  },
];

const qualityPoints = [
  'Namen, plekken en inside jokes verwerkt in de tekst',
  'Heldere intake zodat we meteen de juiste sfeer pakken',
  'Eén afgewerkte versie, geen losse demo of schets',
  'Past bij vrolijke, emotionele, grappige en energieke momenten',
];

const testimonials = [
  { quote: 'Op het geslaagd-feest gedraaid en iedereen ging los. Mijn naam zat er gewoon in!', name: 'Annelies V.' },
  { quote: 'Voor mijn vaders 60e. Snel geleverd en precies de juiste sfeer.', name: 'Thomas de B.' },
  { quote: 'Betaalbaar en verrassend persoonlijk. Op Spotify gezet en met de hele familie gedeeld.', name: 'Sarah L.' },
];

const faqPreview = [
  { q: 'Zijn de nummers met AI gemaakt?', a: 'Ja. We werken met kant-en-klare, deels al gemaakte bouwstenen en personaliseren de tekst met AI op basis van jouw verhaal. Daardoor is het snel én betaalbaar.' },
  { q: 'Wat krijg ik precies?', a: 'Eén afgewerkte versie van je nummer. Diezelfde versie deel je overal — op Spotify, Apple Music en social media.' },
  { q: 'Wat kost het?', a: offer.saleOn.value ? `Normaal ${offer.formattedRegular}, nu in de aanbieding ${offer.formattedSale} per nummer.` : `${offer.formattedRegular} per nummer.` },
];
</script>

<template>
  <div>
    <!-- HERO -->
    <section class="relative overflow-hidden py-8 sm:py-12 lg:py-16">
      <div class="wide-container">
        <div class="site-frame grid min-h-[620px] items-center gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[1.04fr_0.96fr] lg:px-12 lg:py-14">
          <div>
            <span v-reveal class="section-kicker">Jouw verhaal als lied</span>
            <h1 v-reveal class="section-heading max-w-4xl text-5xl leading-[1.04] sm:text-6xl lg:text-7xl">
              Een persoonlijk nummer voor ieder moment.
            </h1>
            <p v-reveal class="mt-7 max-w-2xl text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
              Vertel ons wie je wilt verrassen en waarom dit moment telt. Wij maken er een
              afgewerkt nummer van met namen, anekdotes en precies de juiste sfeer.
            </p>
            <div v-reveal class="mt-9 flex flex-col gap-3 sm:flex-row">
              <NuxtLink to="/aanvraag" class="stitch-button px-8 py-3.5">Start je aanvraag</NuxtLink>
              <NuxtLink to="/momenten" class="stitch-outline-button px-8 py-3.5">Bekijk momenten</NuxtLink>
            </div>
            <div v-reveal class="mt-9 grid gap-3 sm:grid-cols-3">
              <div v-for="stat in heroStats" :key="stat.value" class="metric-tile">
                <p class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">{{ stat.value }}</p>
                <p class="mt-1 text-xs font-semibold leading-5" :style="{ color: 'var(--color-ink-soft)' }">{{ stat.label }}</p>
              </div>
            </div>
          </div>

          <div v-reveal class="relative">
            <div class="relative overflow-hidden border bg-[var(--color-bg-contrast)] p-5 text-white shadow-[0_24px_70px_rgba(23,33,29,0.22)]" style="border-radius: 8px; border-color: rgba(255,255,255,0.14);">
              <div class="grid gap-4 sm:grid-cols-[0.95fr_1.05fr]">
                <div class="flex aspect-square items-center justify-center bg-white/10" style="border-radius: 8px;">
                  <div class="text-center">
                    <p class="font-display text-6xl">♪</p>
                    <p class="mt-3 text-xs font-bold uppercase tracking-[0.18em] text-white/60">Voor Ieder Moment</p>
                  </div>
                </div>
                <div class="flex flex-col justify-between gap-6">
                  <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--accent-gold);">Nu in productie</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold leading-tight">Voor Sophie, die eindelijk geslaagd is</h2>
                    <p class="mt-4 text-sm leading-6 text-white/70">
                      Met haar naam, de vlag aan de gevel, die ene docent en de grap uit de klas.
                    </p>
                  </div>
                  <div class="space-y-3">
                    <div class="h-2 w-full bg-white/10" style="border-radius: 999px;">
                      <div class="h-2 w-2/3 accent-gradient" style="border-radius: 999px;" />
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold text-white/55">
                      <span>Tekst</span>
                      <span>Refrein</span>
                      <span>Mix</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="border border-white/10 bg-white/[0.06] p-4" style="border-radius: 8px;">
                  <p class="text-xs font-bold uppercase tracking-[0.14em] text-white/45">Sfeer</p>
                  <p class="mt-2 font-semibold">Vrolijk &amp; uptempo</p>
                </div>
                <div class="border border-white/10 bg-white/[0.06] p-4" style="border-radius: 8px;">
                  <p class="text-xs font-bold uppercase tracking-[0.14em] text-white/45">Moment</p>
                  <p class="mt-2 font-semibold">Geslaagd</p>
                </div>
                <div class="border border-white/10 bg-white/[0.06] p-4" style="border-radius: 8px;">
                  <p class="text-xs font-bold uppercase tracking-[0.14em] text-white/45">Delen</p>
                  <p class="mt-2 font-semibold">Spotify-ready</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- WAARDE -->
    <section class="site-container py-12 sm:py-16">
      <div class="grid gap-4 md:grid-cols-3">
        <article v-for="item in valueProps" :key="item.title" v-reveal class="rich-card p-7">
          <div class="mb-5 h-1.5 w-16 accent-gradient" style="border-radius: 999px;" />
          <h2 class="font-display text-2xl font-semibold leading-tight" :style="{ color: 'var(--color-ink)' }">{{ item.title }}</h2>
          <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.body }}</p>
        </article>
      </div>
    </section>

    <!-- MOMENTEN -->
    <section class="py-16 sm:py-20" :style="{ background: 'linear-gradient(180deg, transparent, var(--color-surface-soft))' }">
      <div class="site-container">
        <div v-reveal class="mb-10 grid gap-6 md:grid-cols-[0.9fr_1.1fr] md:items-end">
          <div>
            <span class="section-kicker">Momenten</span>
            <h2 class="section-heading text-4xl md:text-5xl">Kies waar het nummer over mag gaan</h2>
          </div>
          <p class="section-subtext max-w-2xl md:justify-self-end">
            Van geslaagd en rijbewijs tot Vaderdag, Moederdag of een eigen huis. Voor bekende
            momenten staat de basis al klaar; jouw details maken het nummer echt persoonlijk.
          </p>
        </div>
        <div v-reveal class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <MomentCard v-for="c in consumerCategories" :key="c.slug" :category="c" />
        </div>
        <div v-reveal class="mt-8 flex justify-center">
          <NuxtLink to="/momenten" class="stitch-outline-button">Alle momenten bekijken</NuxtLink>
        </div>
      </div>
    </section>

    <!-- KWALITEIT -->
    <section class="site-container py-16 sm:py-20">
      <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <div v-reveal>
          <span class="section-kicker">Waarom het werkt</span>
          <h2 class="section-heading text-4xl md:text-5xl">Geen standaard liedje, maar een verhaal dat klopt</h2>
          <p class="section-subtext mt-5 max-w-xl">
            Een goed persoonlijk nummer draait niet om zoveel mogelijk informatie, maar om de juiste details.
            Daarom sturen we je aanvraag met gerichte vragen en bouwen we het lied rond herkenbare momenten.
          </p>
        </div>
        <div v-reveal class="rich-card p-6 sm:p-8">
          <ul class="grid gap-4 sm:grid-cols-2">
            <li v-for="point in qualityPoints" :key="point" class="flex gap-3 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">
              <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" :style="{ background: 'var(--accent-alt-soft)', color: 'var(--accent-alt)' }">✓</span>
              <span>{{ point }}</span>
            </li>
          </ul>
          <div class="mt-8 border-t pt-6" :style="{ borderColor: 'var(--color-line)' }">
            <p class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">Klaar voor het feest, de verrassing of de groepsapp.</p>
            <p class="mt-2 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">
              Jij levert de details aan. Wij maken er één duidelijke, deelbare versie van.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- HOE HET WERKT -->
    <section class="py-16 sm:py-20" :style="{ background: 'var(--color-bg-contrast)' }">
      <div class="site-container">
        <div v-reveal class="mb-12 grid gap-5 md:grid-cols-[0.8fr_1fr] md:items-end">
          <div>
            <span class="section-kicker" :style="{ background: 'rgba(255,255,255,0.08)', borderColor: 'rgba(255,255,255,0.14)', color: 'var(--accent-gold)' }">Zo werkt het</span>
            <h2 class="font-display text-4xl font-semibold text-white md:text-5xl">In vier stappen klaar</h2>
          </div>
          <p class="text-base leading-7 text-white/68">
            We houden het proces kort en helder. Jij hoeft geen briefing van tien pagina's te schrijven;
            de juiste persoonlijke details zijn genoeg.
          </p>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
          <article v-for="s in steps" :key="s.n" v-reveal class="border border-white/10 bg-white/[0.06] p-6" style="border-radius: 8px;">
            <p class="font-display text-4xl font-semibold" style="color: var(--accent-gold);">{{ s.n }}</p>
            <h3 class="mt-5 font-display text-xl font-semibold text-white">{{ s.t }}</h3>
            <p class="mt-3 text-sm leading-6 text-white/64">{{ s.d }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- SOCIAL PROOF -->
    <section class="site-container py-16 sm:py-20">
      <div v-reveal class="mb-10 text-center">
        <span class="section-kicker">Reacties</span>
        <h2 class="section-heading text-4xl md:text-5xl">Een verrassing die mensen onthouden</h2>
      </div>
      <div class="grid gap-6 md:grid-cols-3">
        <article v-for="t in testimonials" :key="t.name" v-reveal class="soft-card p-8">
          <p class="font-display text-2xl italic leading-8" :style="{ color: 'var(--color-ink)' }">“{{ t.quote }}”</p>
          <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">{{ t.name }}</p>
        </article>
      </div>
    </section>

    <!-- PRIJS -->
    <section class="site-container py-16 sm:py-20">
      <div v-reveal class="site-frame grid gap-8 p-7 sm:p-10 lg:grid-cols-[1fr_0.8fr] lg:items-center">
        <div>
          <span class="section-kicker">Eén helder tarief</span>
          <h2 class="section-heading text-4xl md:text-5xl">Een compleet persoonlijk nummer zonder onduidelijke pakketten</h2>
          <p class="section-subtext mt-5 max-w-2xl">
            Je vraagt één nummer aan en krijgt één afgewerkte versie die je direct kunt delen.
            Geen verborgen abonnementen, geen extra keuzestress.
          </p>
          <ul class="mt-8 grid gap-3 text-sm sm:grid-cols-2" :style="{ color: 'var(--color-ink-soft)' }">
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-alt)' }">✓</span> Gepersonaliseerde tekst</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-alt)' }">✓</span> Afgewerkt nummer</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-alt)' }">✓</span> Deelbaar op social media</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-alt)' }">✓</span> Spotify &amp; Apple Music-ready</li>
          </ul>
        </div>
        <div class="rich-card p-7 text-center sm:p-8">
          <p class="text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">Vanaf</p>
          <div class="mt-4 flex justify-center">
            <OfferBadge size="lg" />
          </div>
          <p class="mx-auto mt-5 max-w-xs text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">
            Ideaal als cadeau, feestopening of persoonlijke verrassing.
          </p>
          <NuxtLink to="/aanvraag" class="stitch-button mt-7 w-full px-8 py-4">Vraag jouw nummer aan</NuxtLink>
        </div>
      </div>
    </section>

    <!-- FAQ PREVIEW -->
    <section class="site-container py-16 sm:py-20">
      <div class="mx-auto max-w-3xl">
        <div v-reveal class="mb-8 text-center">
          <span class="section-kicker">Goed om te weten</span>
          <h2 class="section-heading text-3xl md:text-4xl">Veelgestelde vragen</h2>
        </div>
        <div class="space-y-3">
          <details v-for="item in faqPreview" :key="item.q" v-reveal class="rich-card p-6">
            <summary class="cursor-pointer list-none font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.q }}</summary>
            <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.a }}</p>
          </details>
        </div>
        <div class="mt-8 text-center">
          <NuxtLink to="/faq-contact" class="text-sm font-bold" :style="{ color: 'var(--accent-strong)' }">Alle vragen &amp; contact →</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>
