<script setup lang="ts">
import { consumerCategories, categoryPath } from '~/data/categories';

const route = useRoute();
const isOpen = ref(false);
const momentsOpen = ref(false);

const links = [
  { to: '/', label: 'Home' },
  { to: '/momenten', label: 'Momenten' },
  { to: '/zakelijk/bouwbedrijven', label: 'Zakelijk' },
  { to: '/over-ons', label: 'Over ons' },
  { to: '/faq-contact', label: 'FAQ & contact' },
];

watch(
  () => route.path,
  () => {
    isOpen.value = false;
    momentsOpen.value = false;
  },
);
</script>

<template>
  <header
    class="sticky top-0 z-50 border-b backdrop-blur-2xl"
    :style="{ borderColor: 'rgba(16,26,22,0.08)', background: 'rgba(244,246,239,0.78)' }"
  >
    <div class="wide-container flex min-h-16 items-center justify-between gap-4 py-3 lg:min-h-[4.75rem]">
      <NuxtLink to="/" class="flex min-w-0 items-center gap-2.5">
        <span class="logo-card">
          <img src="/logowit.png" alt="" class="h-4 w-auto sm:h-5" />
        </span>
        <span class="truncate font-display text-base font-semibold" :style="{ color: 'var(--color-ink)' }">
          Voor Ieder Moment
        </span>
      </NuxtLink>

      <nav class="hidden items-center gap-1 lg:flex">
        <div
          v-for="link in links"
          :key="link.to"
          class="relative"
          @mouseenter="link.to === '/momenten' && (momentsOpen = true)"
          @mouseleave="link.to === '/momenten' && (momentsOpen = false)"
        >
          <NuxtLink
            :to="link.to"
            class="rounded-lg px-4 py-2.5 text-sm font-semibold transition duration-200"
            :style="route.path === link.to || (link.to !== '/' && route.path.startsWith(link.to))
              ? { color: 'var(--accent-strong)', background: 'var(--accent-soft)' }
              : { color: 'var(--color-ink-soft)' }"
          >
            {{ link.label }}
          </NuxtLink>

          <div
            v-if="link.to === '/momenten' && momentsOpen"
            class="absolute left-0 top-full w-64 pt-2"
          >
            <div class="panel grid gap-1 p-2">
              <NuxtLink
                v-for="c in consumerCategories"
                :key="c.slug"
                :to="categoryPath(c)"
                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-[var(--color-surface-soft)]"
                :style="{ color: 'var(--color-ink)' }"
              >
                <span aria-hidden="true">{{ c.emoji }}</span>{{ c.title }}
              </NuxtLink>
            </div>
          </div>
        </div>
      </nav>

      <div class="hidden lg:block">
        <NuxtLink to="/aanvraag" class="stitch-button">Start aanvraag</NuxtLink>
      </div>

      <button
        class="inline-flex h-11 w-11 items-center justify-center rounded-full border lg:hidden"
        :style="{ borderColor: 'var(--color-line-strong)', color: 'var(--color-ink)' }"
        @click="isOpen = !isOpen"
        :aria-expanded="isOpen"
        aria-label="Menu openen"
      >
        <span class="text-xl">{{ isOpen ? '×' : '☰' }}</span>
      </button>
    </div>

    <div v-if="isOpen" class="border-t lg:hidden" :style="{ borderColor: 'var(--color-line)', background: 'var(--color-bg)' }">
      <div class="wide-container flex flex-col gap-2 py-5">
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="rounded-lg border px-4 py-3.5 font-display text-base"
          :style="{ borderColor: 'var(--color-line)', color: 'var(--color-ink)' }"
        >
          {{ link.label }}
        </NuxtLink>
        <NuxtLink to="/aanvraag" class="stitch-button mt-2 w-full">Start aanvraag</NuxtLink>
      </div>
    </div>
  </header>
</template>
