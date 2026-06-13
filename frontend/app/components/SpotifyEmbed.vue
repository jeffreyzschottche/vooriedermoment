<script setup lang="ts">
// Spotify embed (track of playlist). Zonder src toont het een nette placeholder,
// zodat pagina's al kloppen vóór de echte playlist gedeeld is.
withDefaults(
  defineProps<{
    src?: string | null;
    title?: string;
    height?: number;
    placeholderText?: string;
  }>(),
  {
    src: null,
    title: 'Spotify',
    height: 352,
    placeholderText: 'De playlist komt hier binnenkort te staan.',
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
    <div
      v-else
      class="flex flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed px-6 text-center"
      :style="{ height: height + 'px', borderColor: 'var(--color-line-strong)', background: 'var(--color-surface-soft)' }"
    >
      <span
        class="flex h-12 w-12 items-center justify-center rounded-full text-xl"
        :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }"
        aria-hidden="true"
      >♪</span>
      <p class="max-w-xs text-sm font-medium" :style="{ color: 'var(--color-ink-soft)' }">
        {{ placeholderText }}
      </p>
    </div>
  </div>
</template>
