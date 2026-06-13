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
    <section class="relative overflow-hidden">
      <div class="site-container relative py-20 text-center sm:py-28">
        <span v-reveal class="section-kicker">Jouw verhaal, jouw nummer</span>
        <h1 v-reveal class="section-heading mx-auto max-w-4xl text-5xl leading-[1.05] sm:text-6xl md:text-7xl">
          Voor ieder moment
          <span class="block italic" :style="{ color: 'var(--accent-strong)' }">een eigen nummer.</span>
        </h1>
        <p v-reveal class="mx-auto mt-7 max-w-2xl text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
          Geslaagd, rijbewijs gehaald, een eigen huis of gewoon een verjaardag — wij maken er een
          persoonlijk nummer van. Slim samengesteld en met AI gepersonaliseerd, zodat het
          <strong>snel en betaalbaar</strong> is.
        </p>
        <div v-reveal class="mt-9 flex flex-col items-center justify-center gap-4 sm:flex-row">
          <NuxtLink to="/aanvraag" class="stitch-button px-8 py-3.5">Vraag een nummer aan</NuxtLink>
          <NuxtLink to="/momenten" class="stitch-outline-button px-8 py-3.5">Bekijk alle momenten</NuxtLink>
        </div>
        <div v-reveal class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-semibold" :style="{ color: 'var(--color-ink-soft)' }">
          <span>Snel klaar</span><span aria-hidden="true">·</span>
          <span>Spotify &amp; Apple Music</span><span aria-hidden="true">·</span>
          <span>1 vaste versie om overal te delen</span>
        </div>
      </div>
    </section>

    <!-- MOMENTEN -->
    <section class="site-container py-16 sm:py-20">
      <div v-reveal class="mb-10 text-center">
        <h2 class="section-heading text-4xl md:text-5xl">Kies jouw moment</h2>
        <p class="section-subtext mx-auto mt-3 max-w-2xl">
          Voor de bekendste momenten hebben we al een nummer klaarstaan. Mist jouw naam? Dan maken we hem aan.
        </p>
      </div>
      <div v-reveal class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <MomentCard v-for="c in consumerCategories" :key="c.slug" :category="c" />
      </div>
    </section>

    <!-- HOE HET WERKT -->
    <section class="py-16 sm:py-20" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container">
        <div v-reveal class="mb-12 text-center">
          <span class="section-kicker">Zo werkt het</span>
          <h2 class="section-heading text-4xl md:text-5xl">In vier stappen klaar</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-4">
          <article v-for="s in steps" :key="s.n" v-reveal class="rich-card p-7">
            <p class="font-display text-4xl font-semibold" :style="{ color: 'var(--accent-strong)' }">{{ s.n }}</p>
            <h3 class="mt-4 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">{{ s.t }}</h3>
            <p class="mt-2 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">{{ s.d }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- SOCIAL PROOF -->
    <section class="site-container py-16 sm:py-20">
      <h2 v-reveal class="section-heading mb-10 text-center text-4xl md:text-5xl">Wat klanten zeggen</h2>
      <div class="grid gap-6 md:grid-cols-3">
        <article v-for="t in testimonials" :key="t.name" v-reveal class="soft-card p-8 text-center">
          <p class="font-display text-2xl italic leading-8" :style="{ color: 'var(--color-ink)' }">“{{ t.quote }}”</p>
          <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">{{ t.name }}</p>
        </article>
      </div>
    </section>

    <!-- PRIJS -->
    <section class="py-16 sm:py-20" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container max-w-2xl">
        <div v-reveal class="rich-card p-8 text-center sm:p-12">
          <span class="section-kicker">Eén helder tarief</span>
          <h2 class="section-heading mt-2 text-3xl md:text-4xl">Eén nummer, één prijs</h2>
          <div class="mt-6 flex justify-center">
            <OfferBadge size="lg" />
          </div>
          <ul class="mx-auto mt-8 max-w-md space-y-3 text-left text-sm" :style="{ color: 'var(--color-ink-soft)' }">
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-strong)' }">✓</span> Eén afgewerkt, gepersonaliseerd nummer</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-strong)' }">✓</span> Overal beschikbaar op Spotify &amp; Apple Music</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-strong)' }">✓</span> Eén vaste versie die je overal deelt</li>
            <li class="flex gap-3"><span :style="{ color: 'var(--accent-strong)' }">✓</span> Snel klaar, geen gedoe</li>
          </ul>
          <NuxtLink to="/aanvraag" class="stitch-button mt-9 px-10 py-4">Vraag jouw nummer aan</NuxtLink>
        </div>
      </div>
    </section>

    <!-- FAQ PREVIEW -->
    <section class="site-container py-16 sm:py-20">
      <div class="mx-auto max-w-3xl">
        <h2 v-reveal class="section-heading mb-8 text-center text-3xl md:text-4xl">Veelgestelde vragen</h2>
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
