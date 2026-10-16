<script setup lang="ts">
import { categoryPath, requestCategories } from '~/data/categories';

useSeoMeta({
  title: 'Alle momenten',
  description:
    'Laat een persoonlijk nummer maken voor een verjaardag, rijbewijs, diploma, nieuw huis, geboorte, team, bedrijf en meer.',
  ogTitle: 'Alle momenten — persoonlijke nummers | Voor Ieder Moment',
  ogDescription: 'Laat een persoonlijk nummer maken voor een verjaardag, rijbewijs, diploma, nieuw huis, geboorte, team, bedrijf en meer.',
  twitterTitle: 'Alle momenten — persoonlijke nummers | Voor Ieder Moment',
  twitterDescription: 'Laat een persoonlijk nummer maken voor een verjaardag, rijbewijs, diploma, nieuw huis, geboorte, team, bedrijf en meer.',
});

useJsonLd([
  breadcrumbSchema([
    { name: 'Home', path: '/' },
    { name: 'Momenten', path: '/momenten' },
  ]),
  itemListSchema(requestCategories.map((c) => ({ name: c.title, path: categoryPath(c) }))),
]);
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

    <div v-reveal class="mt-12 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3">
      <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
    </div>
  </div>
</template>
