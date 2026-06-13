<script setup lang="ts">
import { requestCategories } from '~/data/categories';

useSeoMeta({
  title: 'Alle momenten',
  description:
    'Voor welk moment wil je een nummer? Geslaagd, rijbewijs, eigen huis, vaderdag, moederdag, geboorte, verjaardag of een clublied.',
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
      <h1 class="section-heading text-4xl md:text-6xl">Voor welk moment wordt het?</h1>
      <p class="section-subtext mx-auto mt-5 max-w-2xl">
        Kies hieronder je moment. Van persoonlijk cadeau tot teamlied of bedrijfsnummer:
        elk formulier vraagt precies de context die we nodig hebben.
      </p>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
    </div>
  </div>
</template>
