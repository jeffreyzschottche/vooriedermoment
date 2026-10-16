<script setup lang="ts">
import { reviews } from '~/data/reviews';

const average = computed(() =>
  reviews.length ? reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length : 0,
);
</script>

<template>
  <section v-if="reviews.length" class="py-20 sm:py-28">
    <div class="site-container">
      <div v-reveal class="mb-12 max-w-3xl">
        <span class="section-kicker">Ervaringen</span>
        <h2 class="section-heading text-4xl sm:text-5xl">Wat klanten zeggen</h2>
        <p class="section-subtext mt-4">
          Gemiddeld {{ average.toFixed(1).replace('.', ',') }} van 5 sterren op basis van
          {{ reviews.length }} {{ reviews.length === 1 ? 'review' : 'reviews' }}.
        </p>
      </div>

      <div v-reveal-stagger data-stagger="0.08" class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <figure v-for="review in reviews" :key="review.name + review.date" class="bento-card">
          <p class="text-lg tracking-widest" style="color: var(--accent-gold);" :aria-label="`${review.rating} van 5 sterren`">
            {{ '★'.repeat(review.rating) }}<span class="opacity-25">{{ '★'.repeat(5 - review.rating) }}</span>
          </p>
          <blockquote class="mt-4 text-base leading-7" :style="{ color: 'var(--color-ink-soft)' }">
            “{{ review.text }}”
          </blockquote>
          <figcaption class="mt-5 text-sm font-semibold" :style="{ color: 'var(--color-ink)' }">
            {{ review.name }} · <span class="font-medium opacity-70">{{ review.moment }}</span>
          </figcaption>
        </figure>
      </div>
    </div>
  </section>
</template>
