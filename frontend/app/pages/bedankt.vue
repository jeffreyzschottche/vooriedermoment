<script setup lang="ts">
useSeoMeta({
  title: 'Bedankt voor je aanvraag',
  description: 'Je aanvraag is ontvangen.',
});

const { current, lastPayload } = useSongRequest();
const paid = computed(() => ['paid', 'producing', 'music_prompt_ready', 'production_ready'].includes(current.value?.status ?? ''));
const productionReady = computed(() => ['music_prompt_ready', 'production_ready'].includes(current.value?.status ?? ''));
</script>

<template>
  <div class="site-container py-20 sm:py-28">
    <div class="mx-auto max-w-2xl text-center">
      <span
        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full text-3xl"
        :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }"
        aria-hidden="true"
      >✓</span>
      <h1 class="section-heading mt-6 text-4xl md:text-5xl">Bedankt{{ lastPayload ? '!' : '' }}</h1>

      <p v-if="paid" class="mt-5 text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
        Je betaling is gelukt en je aanvraag voor een nummer over
        <strong>{{ lastPayload?.categoryTitle }}</strong> staat genoteerd.
        <span v-if="productionReady">De lyrics en muziekprompt zijn aangemaakt.</span>
        <span v-else>De productie is gestart.</span>
      </p>
      <p v-else class="mt-5 text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
        Je aanvraag is ontvangen. We nemen contact met je op om hem af te ronden.
      </p>

      <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
        <NuxtLink to="/" class="stitch-button px-8 py-3.5">Terug naar home</NuxtLink>
        <NuxtLink to="/momenten" class="stitch-outline-button px-8 py-3.5">Nog een moment vieren</NuxtLink>
      </div>

      <div v-if="paid" class="mt-10 grid gap-3 text-left sm:grid-cols-2">
        <div class="rich-card p-5">
          <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">Stap 1</p>
          <p class="mt-2 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">Lyrics</p>
          <p class="mt-2 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">Definitieve tekst gemaakt uit jouw formuliercontext en herbruikbare rijmblokken.</p>
        </div>
        <div class="rich-card p-5">
          <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">Stap 2</p>
          <p class="mt-2 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">Muziek</p>
          <p class="mt-2 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">Muziekprompt klaargezet voor de provider. Referentie: {{ current?.music_reference ?? 'wordt aangemaakt' }}.</p>
        </div>
      </div>

      <p class="mt-10 text-xs" :style="{ color: 'var(--color-ink-faint)' }">
        Vragen over je bestelling? Mail <a class="underline" href="mailto:info@vooriedermoment.nl">info@vooriedermoment.nl</a>.
      </p>
    </div>
  </div>
</template>
