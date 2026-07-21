<script setup lang="ts">
import {
  otherMomentCategory,
  requestCategories,
} from '~/data/categories';

useSeoMeta({
  title: 'Vraag een nummer aan',
  description: 'Kies je moment en vertel je verhaal. Wij maken er een persoonlijk nummer van.',
});

</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <header v-reveal class="site-frame grid gap-8 p-7 sm:p-10 lg:grid-cols-[1fr_0.8fr] lg:items-center">
      <div>
        <span class="section-kicker">Aanvragen</span>
        <h1 class="section-heading text-4xl md:text-6xl">Kies het moment dat je wilt vereeuwigen</h1>
        <p class="section-subtext mt-5 max-w-2xl">
          Elk verhaal vraagt om andere details. Daarom krijg je vragen die passen bij jouw moment,
          van herinneringen en inside jokes tot een slogan, clubcultuur of bijzondere gebeurtenis.
        </p>
      </div>
      <div class="grid gap-3 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 1:</strong> kies jouw moment</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 2:</strong> vertel wat het persoonlijk maakt</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 3:</strong> kies uit verschillende opties</div>
      </div>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
    </div>

    <div id="ander-moment" class="mx-auto mt-16 max-w-4xl scroll-mt-32">
      <div v-reveal class="mb-6 text-center">
        <span class="section-kicker">Helemaal jouw gelegenheid</span>
        <h2 class="section-heading text-2xl md:text-3xl">Maak een nummer voor een ander moment</h2>
        <p class="section-subtext mt-2">Pensioen, jubileum, afscheid, bedankje of iets dat alleen voor jullie betekenis heeft.</p>
      </div>
      <div v-reveal>
        <IntakeForm :category="otherMomentCategory" />
      </div>
    </div>
  </div>
</template>
