<script setup lang="ts">
useSeoMeta({
  title: 'Bedankt voor je aanvraag',
  description: 'Je aanvraag is ontvangen.',
});

import { themeVars } from '~/data/categories';

const { current, lastPayload, theme } = useSongRequest();
const paid = computed(() => ['paid', 'producing', 'music_prompt_ready', 'production_ready'].includes(current.value?.status ?? ''));

// Zelfde categoriekleur als in de rest van de funnel.
const themeStyle = computed(() => themeVars(theme.value));
</script>

<template>
  <div class="py-20 sm:py-32" :style="themeStyle">
    <div class="site-container">
      <div class="mx-auto max-w-2xl text-center">
        <!-- Success icon -->
        <div
          v-hero-reveal
          class="mx-auto flex h-20 w-20 items-center justify-center rounded-full text-4xl"
          :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }"
        >
          <span class="animate-bounce">✓</span>
        </div>

        <!-- Title -->
        <h1
          v-hero-reveal
          data-hero-delay="0.1"
          class="section-heading mt-8 text-4xl sm:text-5xl lg:text-6xl"
        >
          Bedankt{{ lastPayload ? '!' : '' }}
        </h1>

        <!-- Status message -->
        <p
          v-hero-reveal
          data-hero-delay="0.2"
          v-if="paid"
          class="mt-6 text-lg leading-relaxed sm:text-xl"
          :style="{ color: 'var(--color-ink-soft)' }"
        >
          Je betaling is gelukt en je aanvraag voor een
          <strong :style="{ color: 'var(--accent-strong)' }">{{ lastPayload?.categoryTitle }}</strong>
          nummer staat genoteerd.
          Je ontvangt binnen 48 uur 4 samples in je mail.
        </p>
        <p
          v-hero-reveal
          data-hero-delay="0.2"
          v-else
          class="mt-6 text-lg leading-relaxed sm:text-xl"
          :style="{ color: 'var(--color-ink-soft)' }"
        >
          Je aanvraag is ontvangen. We nemen contact met je op om hem af te ronden.
        </p>

        <!-- CTA buttons -->
        <div
          v-hero-reveal
          data-hero-delay="0.3"
          class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
        >
          <NuxtLink to="/" class="stitch-button px-10 py-4">
            Terug naar home
          </NuxtLink>
          <NuxtLink to="/momenten" class="stitch-outline-button px-10 py-4">
            Nog een moment vieren
          </NuxtLink>
        </div>

        <!-- Production steps -->
        <div
          v-if="paid"
          v-reveal
          class="mt-14"
        >
          <div v-reveal-stagger data-stagger="0.1" class="grid gap-5 text-left sm:grid-cols-2">
            <div class="rich-card p-6">
              <div class="flex items-start gap-4">
                <div class="bento-card-icon shrink-0 text-2xl">🎵</div>
                <div>
                  <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">
                    Stap 1
                  </p>
                  <p class="mt-2 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">
                    4 Samples
                  </p>
                  <p class="mt-2 text-sm leading-relaxed" :style="{ color: 'var(--color-ink-soft)' }">
                    Binnen 48 uur ontvang je 4 unieke samples van 15 seconden per mail.
                  </p>
                </div>
              </div>
            </div>

            <div class="rich-card p-6">
              <div class="flex items-start gap-4">
                <div class="bento-card-icon shrink-0 text-2xl">🎧</div>
                <div>
                  <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">
                    Stap 2
                  </p>
                  <p class="mt-2 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">
                    Spotify &amp; Apple Music
                  </p>
                  <p class="mt-2 text-sm leading-relaxed" :style="{ color: 'var(--color-ink-soft)' }">
                    Kies je favoriet; binnen 72 uur staat het op Spotify én Apple Music.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact info -->
        <p
          v-reveal
          class="mt-14 text-sm"
          :style="{ color: 'var(--color-ink-faint)' }"
        >
          Vragen over je bestelling? Mail
          <a
            class="font-semibold underline transition-colors hover:text-[var(--accent-strong)]"
            href="mailto:info@vooriedermoment.nl"
          >
            info@vooriedermoment.nl
          </a>
        </p>
      </div>
    </div>
  </div>
</template>
