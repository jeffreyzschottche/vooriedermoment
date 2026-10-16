<script setup lang="ts">
// "Nu op Spotify"-podium per moment: donker paneel met gloed in de
// categoriekleur, de (donkere) Spotify-speler met een plaat erachter en CTA's.
const props = defineProps<{
  embed: string;
  kicker: string;
  title: string;
  text: string;
  chips?: string[];
  requestHref: string;
  cover?: string | null;
}>();

const openUrl = computed(() => spotifyOpenUrl(props.embed));
</script>

<template>
  <div class="spotify-stage">
    <div class="spotify-stage-glow" aria-hidden="true" />

    <div class="relative grid gap-10 p-6 sm:p-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:gap-14 lg:p-14">
      <div>
        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-white/80">
          <SpotifyLogo class="h-4 w-4" />
          {{ kicker }}
        </span>
        <h2 class="mt-5 font-display text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
          {{ title }}
        </h2>
        <p class="mt-5 max-w-lg text-base leading-7 text-white/65">
          {{ text }}
        </p>

        <ul v-if="chips?.length" class="mt-6 flex flex-wrap gap-2">
          <li
            v-for="chip in chips"
            :key="chip"
            class="rounded-full px-3 py-1.5 text-xs font-semibold"
            style="background: color-mix(in srgb, var(--accent) 18%, transparent); color: color-mix(in srgb, var(--accent) 55%, white);"
          >
            {{ chip }}
          </li>
        </ul>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
          <NuxtLink :to="requestHref" class="stitch-button px-7 py-3.5">Vraag jouw versie aan</NuxtLink>
          <a
            :href="openUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-7 py-3.5 text-sm font-semibold text-white transition hover:border-[#1ed760] hover:bg-white/5"
          >
            <SpotifyLogo class="h-5 w-5" />
            Open in Spotify
          </a>
        </div>
      </div>

      <div class="relative lg:pr-16">
        <!-- Decoratieve plaat die achter de speler uitsteekt -->
        <div class="vinyl hidden lg:block" aria-hidden="true">
          <div class="vinyl-label" :style="cover ? { backgroundImage: `url(${cover})` } : {}" />
        </div>
        <div class="relative rounded-2xl p-1.5" style="background: #18211d; box-shadow: 0 30px 80px -20px rgba(0,0,0,0.6);">
          <SpotifyEmbed :src="embed" :title="title" :height="352" dark />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.spotify-stage {
  position: relative;
  overflow: hidden;
  border-radius: 28px;
  background: linear-gradient(135deg, #0d1512 0%, #142822 55%, #1a3530 100%);
  box-shadow: 0 32px 64px -16px rgba(13, 21, 18, 0.35);
}
.spotify-stage-glow {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 70% at 85% 50%, color-mix(in srgb, var(--accent) 45%, transparent) 0%, transparent 70%),
    radial-gradient(ellipse 40% 50% at 0% 0%, color-mix(in srgb, var(--accent) 20%, transparent) 0%, transparent 70%);
  pointer-events: none;
}
.vinyl {
  position: absolute;
  top: 50%;
  right: -12px;
  width: 320px;
  height: 320px;
  transform: translateY(-50%);
  border-radius: 999px;
  background:
    repeating-radial-gradient(circle at center, #111 0 2px, #1c1c1c 2px 4px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
  animation: spin 14s linear infinite;
}
.vinyl-label {
  position: absolute;
  inset: 34%;
  border-radius: 999px;
  background: var(--accent) center / cover;
  box-shadow: 0 0 0 4px #0d1512;
}
@keyframes spin {
  to { transform: translateY(-50%) rotate(360deg); }
}
@media (prefers-reduced-motion: reduce) {
  .vinyl { animation: none; }
}
</style>
