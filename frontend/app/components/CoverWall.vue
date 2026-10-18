<script setup lang="ts">
// Schuin gekantelde "muur" van momentcovers die langzaam voorbij schuift.
import { requestCategories, categoryImage, categoryPath } from '~/data/categories';

const covers = requestCategories.map((c) => ({ src: categoryImage(c), title: c.title, href: categoryPath(c) }));
const rows = [covers, [...covers].reverse()];
</script>

<template>
  <div class="cover-wall" aria-hidden="true">
    <div class="cover-plane">
      <div v-for="(row, i) in rows" :key="i" class="cover-row" :class="i % 2 ? 'is-reverse' : ''">
        <div v-for="n in 2" :key="n" class="cover-track">
          <NuxtLink v-for="cover in row" :key="cover.src + n" :to="cover.href" tabindex="-1" class="cover-tile">
            <img :src="cover.src" :alt="cover.title" loading="lazy" />
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cover-wall {
  position: relative;
  height: 420px;
  overflow: hidden;
  perspective: 1400px;
  mask-image: linear-gradient(90deg, transparent 0%, #000 12%, #000 88%, transparent 100%);
}
.cover-plane {
  position: absolute;
  inset: -40px -10%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 20px;
  transform: rotateX(22deg) rotateZ(-7deg);
  transform-style: preserve-3d;
}
.cover-row {
  display: flex;
  width: max-content;
  animation: wall-scroll 60s linear infinite;
}
.cover-row.is-reverse {
  animation-direction: reverse;
  margin-left: -140px;
}
.cover-track {
  display: flex;
  gap: 20px;
  padding-right: 20px;
}
.cover-tile {
  display: block;
  width: 300px;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  border-radius: 22px;
  box-shadow: 0 30px 50px -24px rgba(18, 20, 18, 0.45);
  transition: transform 0.5s var(--ease-out-expo);
}
.cover-tile:hover {
  transform: translateZ(40px) scale(1.04);
}
.cover-tile img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cover-wall:hover .cover-row {
  animation-play-state: paused;
}
@keyframes wall-scroll {
  to { transform: translateX(-50%); }
}
@media (max-width: 640px) {
  .cover-wall { height: 300px; }
  .cover-tile { width: 200px; border-radius: 16px; }
}
@media (prefers-reduced-motion: reduce) {
  .cover-row { animation: none; }
}
</style>
