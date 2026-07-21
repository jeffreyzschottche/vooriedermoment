<script setup lang="ts">
// Spotify embed (track of playlist).
// - met src: de echte iframe.
// - showcase + geen src: rijke kaart zolang de Spotify-embed nog niet gekoppeld is
//   met Spotify-logo en een sfeerbeeld (bv. de categorie-cover).
// - anders geen src: compacte nette placeholder (kleine tegels).
withDefaults(
  defineProps<{
    src?: string | null;
    title?: string;
    height?: number;
    cover?: string | null;
    showcase?: boolean;
    placeholderText?: string;
  }>(),
  {
    src: null,
    title: 'Spotify',
    height: 352,
    cover: null,
    showcase: false,
    placeholderText: 'Voorbeeld komt binnenkort.',
  },
);
</script>

<template>
  <div class="overflow-hidden rounded-lg">
    <iframe
      v-if="src"
      :src="src"
      :title="title"
      width="100%"
      :height="height"
      style="border: 0"
      loading="lazy"
      allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
      allowfullscreen
    />

    <!-- Rijke placeholder zolang de Spotify-embed nog niet gekoppeld is -->
    <div v-else-if="showcase" class="relative overflow-hidden rounded-lg" :style="{ height: height + 'px' }">
      <img v-if="cover" :src="cover" alt="" class="absolute inset-0 h-full w-full object-cover" />
      <div v-else class="absolute inset-0 accent-gradient" />

      <div
        class="absolute inset-0"
        style="background: linear-gradient(180deg, rgba(13,21,18,0.4) 0%, rgba(13,21,18,0.55) 45%, rgba(13,21,18,0.86) 100%)"
      />

      <div class="relative flex h-full flex-col items-center justify-center gap-4 px-6 text-center">
        <svg viewBox="0 0 168 168" class="h-11 w-11" aria-hidden="true">
          <path
            fill="#1ED760"
            d="M83.996.277C37.747.277.253 37.77.253 84.019c0 46.251 37.494 83.741 83.743 83.741 46.254 0 83.744-37.49 83.744-83.741 0-46.246-37.49-83.738-83.745-83.738zm38.404 120.78a5.217 5.217 0 01-7.18 1.73c-19.662-12.01-44.414-14.73-73.564-8.07a5.222 5.222 0 01-6.249-3.93 5.213 5.213 0 013.926-6.25c31.9-7.291 59.263-4.15 81.337 9.34 2.46 1.51 3.24 4.72 1.73 7.18zm10.25-22.805c-1.89 3.075-5.91 4.045-8.98 2.155-22.51-13.839-56.823-17.846-83.448-9.764-3.453 1.043-7.1-.903-8.148-4.35a6.538 6.538 0 014.354-8.143c30.413-9.228 68.222-4.758 94.072 11.127 3.07 1.89 4.04 5.91 2.15 8.98zm.88-23.744c-26.99-16.031-71.52-17.505-97.289-9.684a7.835 7.835 0 01-9.768-5.221 7.834 7.834 0 015.221-9.771c29.581-8.98 78.756-7.245 109.83 11.202a7.823 7.823 0 012.74 10.733c-2.2 3.722-7.02 4.949-10.73 2.741z"
          />
        </svg>

        <div>
          <p class="font-display text-2xl font-semibold leading-tight text-white sm:text-3xl">
            Binnenkort hier te beluisteren
          </p>
          <p class="mt-1.5 text-base font-bold" style="color: #1ed760">
            Maak ondertussen je eigen versie
          </p>
        </div>

        <p class="max-w-xs text-sm leading-relaxed text-white/70">
          {{ placeholderText }}
        </p>
      </div>
    </div>

    <!-- Compacte placeholder (kleine tegels) -->
    <div
      v-else
      class="flex flex-col items-center justify-center gap-3 rounded-lg px-6 text-center"
      :style="{ height: height + 'px', background: 'var(--color-surface-soft)' }"
    >
      <svg viewBox="0 0 168 168" class="h-8 w-8" aria-hidden="true">
        <path
          fill="#1ED760"
          d="M83.996.277C37.747.277.253 37.77.253 84.019c0 46.251 37.494 83.741 83.743 83.741 46.254 0 83.744-37.49 83.744-83.741 0-46.246-37.49-83.738-83.745-83.738zm38.404 120.78a5.217 5.217 0 01-7.18 1.73c-19.662-12.01-44.414-14.73-73.564-8.07a5.222 5.222 0 01-6.249-3.93 5.213 5.213 0 013.926-6.25c31.9-7.291 59.263-4.15 81.337 9.34 2.46 1.51 3.24 4.72 1.73 7.18zm10.25-22.805c-1.89 3.075-5.91 4.045-8.98 2.155-22.51-13.839-56.823-17.846-83.448-9.764-3.453 1.043-7.1-.903-8.148-4.35a6.538 6.538 0 014.354-8.143c30.413-9.228 68.222-4.758 94.072 11.127 3.07 1.89 4.04 5.91 2.15 8.98zm.88-23.744c-26.99-16.031-71.52-17.505-97.289-9.684a7.835 7.835 0 01-9.768-5.221 7.834 7.834 0 015.221-9.771c29.581-8.98 78.756-7.245 109.83 11.202a7.823 7.823 0 012.74 10.733c-2.2 3.722-7.02 4.949-10.73 2.741z"
        />
      </svg>
      <p class="max-w-xs text-sm font-medium" :style="{ color: 'var(--color-ink-soft)' }">
        {{ placeholderText }}
      </p>
    </div>
  </div>
</template>
