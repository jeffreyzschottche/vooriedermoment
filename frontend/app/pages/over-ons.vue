<script setup lang="ts">
useSeoMeta({
  title: 'Over ons',
  description:
    'Voor Ieder Moment maakt persoonlijke nummers betaalbaar door slimme bouwstenen te combineren met AI. Eerlijk, snel en voor iedereen.',
});

useJsonLd(breadcrumbSchema([
  { name: 'Home', path: '/' },
  { name: 'Over ons', path: '/over-ons' },
]));

const steps = [
  { n: '01', t: 'Jij vertelt je verhaal', d: 'Via een kort formulier deel je namen, anekdotes en de sfeer die je voor ogen hebt.' },
  { n: '02', t: 'Wij stellen de tekst samen', d: 'We combineren kant-en-klare, rijmende bouwstenen met AI-tekst die jouw details verwerkt.' },
  { n: '03', t: 'Het nummer wordt gemaakt', d: 'De tekst wordt omgezet naar een afgewerkt nummer in de gekozen stijl.' },
  { n: '04', t: 'Jij deelt het overal', d: 'Je krijgt één vaste versie, klaar voor Spotify, Apple Music en social media.' },
];
</script>

<template>
  <div>
    <section class="site-container py-16 sm:py-24">
      <div class="mx-auto max-w-3xl text-center">
        <span v-reveal class="section-kicker">Over ons</span>
        <h1 v-reveal class="section-heading text-4xl leading-tight md:text-6xl">
          Een persoonlijk nummer hoeft geen luxe te zijn
        </h1>
        <p v-reveal class="mt-6 text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
          Vroeger was een eigen nummer laten maken duur en traag. Wij doen het anders: door slimme,
          kant-en-klare bouwstenen te combineren met AI, maken we persoonlijke muziek
          <strong>snel en betaalbaar</strong> — voor ieder moment, voor iedereen.
        </p>
      </div>
    </section>

    <section class="py-16 sm:py-20" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container">
        <div v-reveal class="mb-12 max-w-xl">
          <h2 class="section-heading text-3xl md:text-4xl">Hoe wij werken</h2>
          <p class="section-subtext mt-3">Eerlijk verhaal: geen verborgen studio-uren, wél een nummer dat over jou gaat.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <article v-for="s in steps" :key="s.n" v-reveal class="rich-card p-7">
            <p class="font-display text-4xl font-semibold" :style="{ color: 'var(--accent-strong)' }">{{ s.n }}</p>
            <h3 class="mt-4 font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">{{ s.t }}</h3>
            <p class="mt-2 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">{{ s.d }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="site-container py-16 sm:py-20">
      <div v-reveal class="mx-auto max-w-3xl rich-card p-8 sm:p-12">
        <h2 class="section-heading text-2xl md:text-3xl">Transparant over AI</h2>
        <p class="mt-4 leading-8" :style="{ color: 'var(--color-ink-soft)' }">
          We vinden eerlijkheid belangrijk. Onze nummers zijn deels samengesteld uit vooraf gemaakte,
          rijmende bouwstenen en worden met AI gepersonaliseerd op basis van wat jij aanlevert. Dat is
          precies waarom we het zo snel en voordelig kunnen aanbieden — zonder dat je inlevert op het
          persoonlijke gevoel.
        </p>
        <NuxtLink to="/aanvraag" class="stitch-button mt-8">Vraag jouw nummer aan</NuxtLink>
      </div>
    </section>

    <section class="py-16 sm:py-20 text-center" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container mx-auto max-w-2xl">
        <h2 v-reveal class="section-heading text-3xl md:text-4xl">Klaar voor jouw moment?</h2>
        <div v-reveal class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
          <NuxtLink to="/momenten" class="stitch-button px-8 py-3.5">Bekijk alle momenten</NuxtLink>
          <NuxtLink to="/faq-contact" class="stitch-outline-button px-8 py-3.5">Stel een vraag</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>
