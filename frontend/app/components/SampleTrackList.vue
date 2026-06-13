<script setup lang="ts">
import type { SampleTrack } from '~/data/categories';

defineProps<{ tracks: SampleTrack[]; columns?: number }>();
</script>

<template>
  <div
    class="grid gap-5"
    :class="{
      'sm:grid-cols-2': (columns ?? 2) >= 2,
      'lg:grid-cols-2': (columns ?? 2) === 2,
      'lg:grid-cols-4': (columns ?? 2) === 4,
    }"
  >
    <article v-for="track in tracks" :key="track.title" class="rich-card overflow-hidden">
      <SpotifyEmbed
        :src="track.spotifyEmbed"
        :title="track.title"
        :height="152"
        placeholder-text="Voorbeeld komt binnenkort"
      />
      <div class="p-5">
        <h3 class="font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">
          {{ track.title }}
        </h3>
        <p class="mt-1 text-sm" :style="{ color: 'var(--color-ink-soft)' }">{{ track.subtitle }}</p>
      </div>
    </article>
  </div>
</template>
