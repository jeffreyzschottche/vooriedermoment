<script setup lang="ts">
import { consumerCategories } from '~/data/categories';

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
        Kies hieronder je moment. Voor de bekendste hebben we al een nummer op Spotify staan — mist
        jouw naam, dan maken we hem aan.
      </p>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in consumerCategories" :key="c.slug" :category="c" />
    </div>

    <div v-reveal class="mt-12">
      <div class="rich-card flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
        <div>
          <h2 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">Een bouwbedrijf?</h2>
          <p class="mt-1 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
            Ook voor bedrijven maken we een eigen nummer — met bedrijfsnaam, slogan en kernwaarden.
          </p>
        </div>
        <NuxtLink to="/zakelijk/bouwbedrijven" class="stitch-outline-button whitespace-nowrap">Naar zakelijk →</NuxtLink>
      </div>
    </div>
  </div>
</template>
