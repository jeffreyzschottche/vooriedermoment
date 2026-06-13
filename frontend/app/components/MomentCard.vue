<script setup lang="ts">
import type { Category } from '~/data/categories';
import { categoryPath } from '~/data/categories';

const props = defineProps<{ category: Category }>();
const href = computed(() => categoryPath(props.category));
const t = computed(() => props.category.theme);
</script>

<template>
  <NuxtLink
    :to="href"
    class="group relative block overflow-hidden border bg-white p-5 transition duration-300 hover:-translate-y-1"
    :style="{
      borderColor: 'var(--color-line)',
      borderRadius: '8px',
      boxShadow: '0 18px 54px rgba(16,26,22,0.08)',
    }"
  >
    <div
      class="absolute inset-x-0 top-0 h-1 opacity-80 transition group-hover:h-1.5"
      :style="{ background: `linear-gradient(90deg, ${t.accent}, ${t.accentStrong})` }"
    />
    <span
      class="flex h-12 w-12 items-center justify-center rounded-lg text-2xl shadow-[0_12px_28px_rgba(16,26,22,0.08)]"
      :style="{ background: t.accentSoft }"
      aria-hidden="true"
    >{{ category.emoji }}</span>
    <h3
      class="mt-4 font-display text-xl font-semibold transition"
      :style="{ color: 'var(--color-ink)' }"
    >
      {{ category.title }}
    </h3>
    <p class="mt-3 line-clamp-3 min-h-[4.5rem] text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">
      {{ category.heroLead }}
    </p>
    <span
      class="mt-5 inline-flex items-center gap-2 text-sm font-bold"
      :style="{ color: t.accentStrong }"
    >
      Bekijk intake
      <span class="transition group-hover:translate-x-0.5">→</span>
    </span>
  </NuxtLink>
</template>
