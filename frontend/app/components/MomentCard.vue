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
    class="group block rounded-3xl border bg-white p-6 transition duration-200 hover:-translate-y-1"
    :style="{
      borderColor: 'var(--color-line)',
      boxShadow: '0 14px 40px rgba(39,31,24,0.07)',
    }"
  >
    <span
      class="flex h-12 w-12 items-center justify-center rounded-2xl text-2xl"
      :style="{ background: t.accentSoft }"
      aria-hidden="true"
    >{{ category.emoji }}</span>
    <h3
      class="mt-4 font-display text-xl font-semibold transition"
      :style="{ color: 'var(--color-ink)' }"
    >
      {{ category.title }}
    </h3>
    <p class="mt-2 line-clamp-2 text-sm leading-6" :style="{ color: 'var(--color-ink-soft)' }">
      {{ category.heroLead }}
    </p>
    <span
      class="mt-4 inline-flex items-center gap-1 text-sm font-bold"
      :style="{ color: t.accentStrong }"
    >
      Bekijk
      <span class="transition group-hover:translate-x-0.5">→</span>
    </span>
  </NuxtLink>
</template>
