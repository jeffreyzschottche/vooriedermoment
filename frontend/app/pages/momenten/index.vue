<script setup lang="ts">
import { requestCategories } from '~/data/categories';

useSeoMeta({
  title: 'Alle momenten',
  description:
    'Laat een persoonlijk nummer maken voor een verjaardag, rijbewijs, diploma, nieuw huis, geboorte, team, bedrijf en meer.',
});

useJsonLd(breadcrumbSchema([
  { name: 'Home', path: '/' },
  { name: 'Momenten', path: '/momenten' },
]));
</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <header v-reveal class="mx-auto max-w-3xl text-center">
      <span class="section-kicker">Alle momenten</span>
      <h1 class="section-heading text-4xl md:text-6xl">Welk verhaal mag een nummer worden?</h1>
      <p class="section-subtext mx-auto mt-5 max-w-2xl">
        Kies de gelegenheid die het beste past. Staat jouw moment er niet tussen?
        Kies dan ‘Een ander moment’ en vertel zelf wat er gevierd, bedankt of herinnerd mag worden.
      </p>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
    </div>
  </div>
</template>
