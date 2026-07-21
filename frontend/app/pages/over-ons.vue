<script setup lang="ts">
useSeoMeta({
  title: 'Over ons',
  description:
    'Voor Ieder Moment maakt van echte verhalen persoonlijke nummers voor Spotify en Apple Music. Persoonlijk, betaalbaar en snel geregeld.',
});

useJsonLd(breadcrumbSchema([
  { name: 'Home', path: '/' },
  { name: 'Over ons', path: '/over-ons' },
]));

const steps = [
  { n: '01', t: 'Jij vertelt wat telt', d: 'Via een korte briefing deel je namen, herinneringen, inside jokes en jouw gewenste sfeer.' },
  { n: '02', t: 'Wij maken verschillende opties', d: 'We vertalen jouw verhaal naar korte muzikale opties om uit te kiezen.' },
  { n: '03', t: 'Jij kiest de favoriet', d: 'Luister thuis of samen en kies de versie die het moment het beste vangt.' },
  { n: '04', t: 'Wij regelen de release', d: 'We maken jouw keuze compleet voor Spotify, Apple Music en social media.' },
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
          Een eigen nummer laten maken was ooit vooral duur en tijdrovend. Wij maken het bereikbaar:
          jouw verhaal, verschillende muzikale opties en één complete release voor
          <strong>€ 9,99</strong> — voor ieder moment en voor iedereen.
        </p>
      </div>
    </section>

    <section class="py-16 sm:py-20" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container">
        <div v-reveal class="mb-12 max-w-xl">
          <h2 class="section-heading text-3xl md:text-4xl">Hoe wij werken</h2>
          <p class="section-subtext mt-3">Geen ingewikkeld studiotraject. Wel een duidelijk proces en een nummer dat echt over jouw moment gaat.</p>
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
