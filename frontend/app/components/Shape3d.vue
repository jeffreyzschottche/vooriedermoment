<script setup lang="ts">
// Decoratieve "3D"-vorm (SVG met licht/schaduw-gradients) die zachtjes zweeft.
// Kleuren volgen het accent van de pagina.
const props = withDefaults(defineProps<{
  kind: 'note' | 'sphere' | 'ring' | 'star' | 'disc';
  size?: number;
  delay?: number;
}>(), { size: 96, delay: 0 });

const uid = useId();
const id = (name: string) => `${name}-${uid}`;
</script>

<template>
  <div
    class="shape-3d pointer-events-none select-none"
    :style="{ width: `${props.size}px`, height: `${props.size}px`, animationDelay: `${-props.delay}s` }"
    aria-hidden="true"
  >
    <svg viewBox="0 0 100 100" class="h-full w-full overflow-visible">
      <defs>
        <radialGradient :id="id('gloss')" cx="32%" cy="28%" r="75%">
          <stop offset="0%" style="stop-color: #fff; stop-opacity: 0.95" />
          <stop offset="18%" style="stop-color: color-mix(in srgb, var(--accent) 35%, white)" />
          <stop offset="60%" style="stop-color: var(--accent)" />
          <stop offset="100%" style="stop-color: var(--accent-strong)" />
        </radialGradient>
        <radialGradient :id="id('gold')" cx="32%" cy="28%" r="75%">
          <stop offset="0%" style="stop-color: #fffbe8" />
          <stop offset="35%" style="stop-color: var(--accent-gold)" />
          <stop offset="100%" style="stop-color: #b9831a" />
        </radialGradient>
        <linearGradient :id="id('stem')" x1="0" x2="1">
          <stop offset="0%" style="stop-color: var(--accent-strong)" />
          <stop offset="45%" style="stop-color: color-mix(in srgb, var(--accent) 60%, white)" />
          <stop offset="100%" style="stop-color: var(--accent-strong)" />
        </linearGradient>
        <linearGradient :id="id('ring')" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" style="stop-color: color-mix(in srgb, var(--accent-gold) 40%, white)" />
          <stop offset="50%" style="stop-color: var(--accent-gold)" />
          <stop offset="100%" style="stop-color: #a8741a" />
        </linearGradient>
        <radialGradient :id="id('vinyl')" cx="35%" cy="30%" r="80%">
          <stop offset="0%" style="stop-color: #3a3f3c" />
          <stop offset="100%" style="stop-color: #0d0f0e" />
        </radialGradient>
        <filter :id="id('soft')" x="-50%" y="-50%" width="200%" height="200%">
          <feGaussianBlur stdDeviation="4" />
        </filter>
      </defs>

      <!-- Schaduw op de "grond" -->
      <ellipse cx="50" cy="96" rx="26" ry="4" fill="rgba(18,20,18,0.18)" :filter="`url(#${id('soft')})`" />

      <g v-if="kind === 'sphere'">
        <circle cx="50" cy="48" r="40" :fill="`url(#${id('gloss')})`" />
        <ellipse cx="36" cy="30" rx="12" ry="7" fill="#fff" opacity="0.55" transform="rotate(-30 36 30)" />
      </g>

      <g v-else-if="kind === 'note'" transform="rotate(-12 50 50)">
        <rect x="58" y="10" width="8" height="62" rx="4" :fill="`url(#${id('stem')})`" />
        <path d="M62 10 C82 16 88 30 80 46 C80 34 72 28 62 26 Z" :fill="`url(#${id('gloss')})`" />
        <ellipse cx="46" cy="74" rx="22" ry="16" :fill="`url(#${id('gloss')})`" transform="rotate(-20 46 74)" />
        <ellipse cx="38" cy="68" rx="7" ry="4" fill="#fff" opacity="0.6" transform="rotate(-30 38 68)" />
      </g>

      <g v-else-if="kind === 'ring'">
        <ellipse cx="50" cy="50" rx="38" ry="38" fill="none" :stroke="`url(#${id('ring')})`" stroke-width="16" />
        <ellipse cx="50" cy="50" rx="38" ry="38" fill="none" stroke="#fff" stroke-opacity="0.45" stroke-width="3" stroke-dasharray="40 200" transform="rotate(-140 50 50)" />
      </g>

      <g v-else-if="kind === 'star'">
        <path
          d="M50 6 C54 34 66 46 94 50 C66 54 54 66 50 94 C46 66 34 54 6 50 C34 46 46 34 50 6 Z"
          :fill="`url(#${id('gold')})`"
        />
        <ellipse cx="44" cy="34" rx="4" ry="9" fill="#fff" opacity="0.6" transform="rotate(20 44 34)" />
      </g>

      <g v-else-if="kind === 'disc'">
        <circle cx="50" cy="50" r="42" :fill="`url(#${id('vinyl')})`" />
        <circle v-for="r in [34, 28, 22]" :key="r" cx="50" cy="50" :r="r" fill="none" stroke="#fff" stroke-opacity="0.07" stroke-width="1" />
        <circle cx="50" cy="50" r="14" :fill="`url(#${id('gloss')})`" />
        <circle cx="50" cy="50" r="2.5" fill="#0d0f0e" />
        <path d="M22 30 A34 34 0 0 1 48 14" fill="none" stroke="#fff" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round" />
      </g>
    </svg>
  </div>
</template>

<style scoped>
.shape-3d {
  animation: shape-float 8s ease-in-out infinite;
  filter: drop-shadow(0 18px 24px rgba(18, 20, 18, 0.18));
}
@keyframes shape-float {
  0%, 100% { transform: translate3d(0, 0, 0) rotate(0deg); }
  50% { transform: translate3d(0, -14px, 0) rotate(6deg); }
}
@media (prefers-reduced-motion: reduce) {
  .shape-3d { animation: none; }
}
</style>
