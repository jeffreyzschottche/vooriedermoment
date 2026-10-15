<script setup lang="ts">
// Spotify embed (track of playlist).
// - met src: de echte iframe.
// - showcase + geen src: rijke kaart zolang de Spotify-embed nog niet gekoppeld is
//   met Spotify-logo en een sfeerbeeld (bv. de categorie-cover).
// - anders geen src: compacte nette placeholder (kleine tegels).
const props = withDefaults(
  defineProps<{
    src?: string | null;
    title?: string;
    height?: number;
    cover?: string | null;
    showcase?: boolean;
    placeholderText?: string;
    // Neutrale donkere speler i.p.v. de kleur van de albumhoes.
    dark?: boolean;
  }>(),
  {
    src: null,
    title: 'Spotify',
    height: 352,
    cover: null,
    showcase: false,
    placeholderText: 'Voorbeeld komt binnenkort.',
    dark: false,
  },
);

const iframeSrc = computed(() => {
  if (!props.src || !props.dark) return props.src;
  return `${props.src}${props.src.includes('?') ? '&' : '?'}theme=0`;
});
</script>

<template>
  <div class="overflow-hidden rounded-lg">
    <iframe
      v-if="src"
      :src="iframeSrc"
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
        <SpotifyLogo class="h-11 w-11" />

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
      <SpotifyLogo class="h-8 w-8" />
      <p class="max-w-xs text-sm font-medium" :style="{ color: 'var(--color-ink-soft)' }">
        {{ placeholderText }}
      </p>
    </div>
  </div>
</template>
