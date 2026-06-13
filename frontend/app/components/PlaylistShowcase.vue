<script setup lang="ts">
// Rijkere playlist-weergave voor de existing-categorieën.
defineProps<{ embed?: string | null; title: string; subtitle?: string }>();
</script>

<template>
  <div class="rich-card overflow-hidden p-3 sm:p-4">
    <div class="grid gap-4 md:grid-cols-[1.1fr_0.9fr] md:items-center">
      <SpotifyEmbed
        :src="embed"
        :title="title"
        :height="380"
        placeholder-text="Deze playlist delen we hier binnenkort. Mis je een naam? Vraag je eigen versie aan."
      />
      <div class="p-4 sm:p-6">
        <span class="section-kicker">Al uitgebracht</span>
        <h3 class="mt-2 font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">
          {{ title }}
        </h3>
        <p v-if="subtitle" class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">
          {{ subtitle }}
        </p>
        <div class="mt-5 flex flex-wrap gap-2">
          <span class="chip">Spotify</span>
          <span class="chip">Apple Music</span>
          <span class="chip">1 gedeelde versie</span>
        </div>
      </div>
    </div>
  </div>
</template>
