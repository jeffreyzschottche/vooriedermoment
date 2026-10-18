<script setup lang="ts">
// Wisselende songtekst + echte Spotify-speler in de homepage-hero.
// Elke 2 sec een ander demonummer; zolang iemand de speler aanraakt of er iets
// afspeelt staat de wissel vast. Rendert als `display: contents`, dus de twee
// kaarten positioneren t.o.v. de hero-visual eromheen.
import { getCategory } from '~/data/categories';
import { heroDemoSlugs, heroDemos } from '~/data/heroDemos';

const ROTATE_MS = 2000;
// Na pauzeren of aanklikken nog even wachten voor de wissel weer start.
const HOLD_MS = 10000;

interface SpotifyController {
  loadUri(uri: string): void;
  addListener(event: 'ready' | 'playback_update', cb: (e: { data: { isPaused: boolean; isBuffering: boolean } }) => void): void;
  destroy(): void;
}
interface SpotifyIframeApi {
  createController(el: HTMLElement, options: { uri: string; width?: string | number; height?: number }, cb: (c: SpotifyController) => void): void;
}

const index = ref(0);
const slide = computed(() => heroDemos[index.value]!);
const category = computed(() => getCategory(slide.value.slug));
const label = computed(() => slide.value.title.replace(/ Voorbeeld (\d)$/, ' · voorbeeld $1'));

const playerWrap = ref<HTMLElement | null>(null);
const ready = ref(false);
const playing = ref(false);
const hovering = ref(false);
const holdUntil = ref(0);
let visible = true;
let controller: SpotifyController | null = null;
let timer: ReturnType<typeof setInterval> | undefined;
let observer: IntersectionObserver | undefined;

function hold() {
  holdUntil.value = Date.now() + HOLD_MS;
}

function show(i: number) {
  index.value = (i + heroDemos.length) % heroDemos.length;
  controller?.loadUri(slide.value.uri);
}

// Spring naar het volgende voorbeeld van een moment (bolletjes onder de speler).
function showMoment(slug: string) {
  const next = heroDemos.findIndex((d, i) => i > index.value && d.slug === slug);
  show(next >= 0 ? next : heroDemos.findIndex((d) => d.slug === slug));
  hold();
}

function tick() {
  if (!ready.value || playing.value || hovering.value || !visible || document.hidden) return;
  if (Date.now() < holdUntil.value) return;
  show(index.value + 1);
}

// Klik in de Spotify-iframe: het venster verliest focus aan die iframe.
function onWindowBlur() {
  setTimeout(() => {
    if (document.activeElement?.tagName === 'IFRAME' && playerWrap.value?.contains(document.activeElement)) hold();
  });
}

function loadSpotifyApi(): Promise<SpotifyIframeApi> {
  const w = window as unknown as {
    __spotifyIframeApi?: Promise<SpotifyIframeApi>;
    onSpotifyIframeApiReady?: (api: SpotifyIframeApi) => void;
  };
  w.__spotifyIframeApi ??= new Promise((resolve) => {
    w.onSpotifyIframeApiReady = resolve;
    const script = document.createElement('script');
    script.src = 'https://open.spotify.com/embed/iframe-api/v1';
    script.async = true;
    document.body.appendChild(script);
  });
  return w.__spotifyIframeApi;
}

onMounted(async () => {
  // Elk bezoek op een willekeurig voorbeeld beginnen (SSR rendert altijd het eerste).
  index.value = Math.floor(Math.random() * heroDemos.length);
  const api = await loadSpotifyApi();
  if (!playerWrap.value) return;
  // De API vervangt het element door de iframe; geef hem een eigen kind zodat Vue z'n DOM houdt.
  const mount = document.createElement('div');
  playerWrap.value.appendChild(mount);
  api.createController(mount, { uri: slide.value.uri, width: '100%', height: 152 }, (c) => {
    controller = c;
    c.addListener('ready', () => (ready.value = true));
    c.addListener('playback_update', (e) => {
      const nowPlaying = !e.data.isPaused || e.data.isBuffering;
      if (playing.value && !nowPlaying) hold();
      playing.value = nowPlaying;
    });
  });

  observer = new IntersectionObserver(([entry]) => (visible = !!entry?.isIntersecting));
  observer.observe(playerWrap.value);
  window.addEventListener('blur', onWindowBlur);
  timer = setInterval(tick, ROTATE_MS);
});

onBeforeUnmount(() => {
  clearInterval(timer);
  observer?.disconnect();
  window.removeEventListener('blur', onWindowBlur);
  controller?.destroy();
});
</script>

<template>
  <div class="contents">
    <!-- Songtekst-kaart -->
    <div class="float-card absolute left-3 top-6 w-56 rounded-3xl border bg-white/95 p-5 backdrop-blur sm:-left-10 sm:top-8 sm:w-64" :style="{ borderColor: 'var(--color-line)' }">
      <Transition name="lyric" mode="out-in">
        <div :key="index">
          <p class="text-[11px] font-bold uppercase tracking-[0.16em]" :style="{ color: category?.theme.accentStrong ?? 'var(--accent-strong)' }">
            Refrein · {{ category?.navLabel }}
          </p>
          <p class="mt-3 min-h-[6.5rem] font-display text-[17px] font-semibold leading-snug sm:min-h-[5.5rem] sm:text-lg" :style="{ color: 'var(--color-ink)' }">
            {{ slide.lyric[0] }}<br />
            {{ slide.lyric[1] }}
          </p>
          <p class="mt-3 truncate text-xs font-semibold" :style="{ color: 'var(--color-ink-soft)' }">♪ {{ label }}</p>
        </div>
      </Transition>
    </div>

    <!-- Echte Spotify-speler -->
    <div
      class="float-card float-card-late absolute -bottom-6 left-4 right-4 rounded-[22px] bg-[#121412]/95 p-2 backdrop-blur sm:-right-8 sm:left-auto sm:w-[22rem]"
      @mouseenter="hovering = true"
      @mouseleave="hovering = false"
      @focusin="hold"
    >
      <div ref="playerWrap" class="relative h-[152px] overflow-hidden rounded-xl">
        <div v-if="!ready" class="absolute inset-0 flex items-center gap-4 bg-white/5 px-4">
          <div class="h-[120px] w-[120px] shrink-0 animate-pulse rounded-lg bg-white/10" />
          <SpotifyLogo class="h-7 w-7 opacity-60" />
        </div>
      </div>

      <div class="flex items-center justify-between gap-3 px-2 pb-1 pt-2.5">
        <div class="flex items-center gap-1.5">
          <button
            v-for="slug in heroDemoSlugs"
            :key="slug"
            type="button"
            class="h-1.5 rounded-full transition-all duration-300"
            :class="slug === slide.slug ? 'w-5' : 'w-1.5 bg-white/25 hover:bg-white/50'"
            :style="slug === slide.slug ? { background: getCategory(slug)?.theme.accent } : undefined"
            :aria-label="`Toon voorbeeld: ${getCategory(slug)?.title}`"
            @click="showMoment(slug)"
          />
        </div>
        <span class="text-[11px] font-medium text-white/50">
          {{ playing ? 'Speelt af' : 'Druk op play' }}
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.float-card {
  box-shadow: 0 24px 50px -20px rgba(18, 20, 18, 0.35);
  animation: float 7s ease-in-out infinite;
}
.float-card-late {
  animation-delay: -3.5s;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}
.lyric-enter-active,
.lyric-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.lyric-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.lyric-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
@media (prefers-reduced-motion: reduce) {
  .float-card { animation: none; }
  .lyric-enter-active,
  .lyric-leave-active { transition: opacity 0.2s ease; }
  .lyric-enter-from,
  .lyric-leave-to { transform: none; }
}
</style>
