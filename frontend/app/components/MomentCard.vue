<script setup lang="ts">
import type { Category } from '~/data/categories';
import { categoryPath, categoryImage } from '~/data/categories';

const props = withDefaults(defineProps<{
  category: Category;
  otherMomentSpan?: 'full' | 'remainder';
}>(), {
  otherMomentSpan: 'full',
});
const href = computed(() => categoryPath(props.category));
const t = computed(() => props.category.theme);
const cover = computed(() => categoryImage(props.category));
</script>

<template>
  <NuxtLink
    :to="href"
    class="group relative overflow-hidden border bg-white transition-all duration-500"
    :class="category.slug === 'anders'
      ? [
          'flex flex-col sm:col-span-2 sm:grid sm:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]',
          otherMomentSpan === 'remainder' ? 'lg:col-span-3' : 'lg:col-span-full',
        ]
      : 'flex flex-col'"
    :style="{
      borderColor: 'var(--color-line)',
      borderRadius: '18px',
      boxShadow: '0 8px 32px rgba(13,21,18,0.06)',
      transitionTimingFunction: 'var(--ease-out-expo)',
    }"
  >
    <!-- Hover glow effect -->
    <div
      class="pointer-events-none absolute -inset-px z-10 rounded-[18px] opacity-0 transition-opacity duration-500 group-hover:opacity-100"
      :style="{ boxShadow: `0 0 40px ${t.accent}20, 0 20px 48px rgba(13,21,18,0.12)` }"
    />

    <!-- Cover (categorie-specifiek) -->
    <div
      class="relative aspect-[16/9] w-full overflow-hidden"
      :class="category.slug === 'anders' ? 'sm:aspect-auto sm:min-h-72' : ''"
    >
      <img
        :src="cover"
        :alt="`${category.title} — persoonlijk nummer`"
        loading="lazy"
        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.06]"
        style="transition-timing-function: var(--ease-out-expo);"
      />
    </div>

    <!-- Body -->
    <div class="flex flex-1 flex-col p-6">
      <h3
        class="font-display text-xl font-semibold transition-colors duration-200 sm:text-2xl"
        :style="{ color: 'var(--color-ink)' }"
      >
        {{ category.title }}
      </h3>

      <p
        class="mt-3 line-clamp-3 flex-1 text-sm leading-relaxed"
        :style="{ color: 'var(--color-ink-soft)' }"
      >
        {{ category.heroLead }}
      </p>

      <span
        class="mt-5 inline-flex items-center gap-2 text-sm font-bold"
        :style="{ color: t.accentStrong }"
      >
        {{ category.slug === 'anders' ? 'Vertel jouw moment' : 'Bekijk intake' }}
        <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
      </span>
    </div>
  </NuxtLink>
</template>

<style scoped>
.group:hover {
  transform: translateY(-6px);
  border-color: v-bind('t.accent + "40"');
}
</style>
