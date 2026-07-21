<script setup lang="ts">
import { consumerCategories, categoryPath } from '~/data/categories';

const route = useRoute();
const isOpen = ref(false);
const momentsOpen = ref(false);

const links = [
  { to: '/', label: 'Home' },
  { to: '/momenten', label: 'Momenten' },
  { to: '/over-ons', label: 'Over ons' },
  { to: '/faq-contact', label: 'FAQ' },
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
    class="sticky top-0 z-50 border-b backdrop-blur-2xl transition-all duration-300"
    :style="{
      borderColor: 'rgba(13,21,18,0.06)',
      background: 'rgba(248,250,246,0.85)',
    }"
  >
    <div class="hero-inner flex min-h-[5.5rem] items-center justify-between gap-6 py-2">
      <!-- Logo -->
      <NuxtLink
        to="/"
        aria-label="Voor Ieder Moment — naar home"
        class="block shrink-0 transition-transform duration-200 hover:scale-[1.02]"
      >
        <img
          src="/logowit.png"
          alt="Voor Ieder Moment"
          class="h-20 w-48 object-cover sm:h-24 sm:w-60"
        />
      </NuxtLink>

      <!-- Desktop nav -->
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
            class="rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200"
            :style="route.path === link.to || (link.to !== '/' && route.path.startsWith(link.to))
              ? { color: 'var(--accent-strong)', background: 'var(--accent-soft)' }
              : { color: 'var(--color-ink-soft)' }"
          >
            {{ link.label }}
          </NuxtLink>

          <!-- Dropdown -->
          <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
          >
            <div
              v-if="link.to === '/momenten' && momentsOpen"
              class="absolute left-0 top-full w-72 pt-3"
            >
              <div class="panel grid gap-1 p-2.5">
                <NuxtLink
                  v-for="c in consumerCategories"
                  :key="c.slug"
                  :to="categoryPath(c)"
                  class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 hover:bg-[var(--color-surface-soft)]"
                  :style="{ color: 'var(--color-ink)' }"
                >
                  <span class="text-lg" aria-hidden="true">{{ c.emoji }}</span>
                  {{ c.title }}
                </NuxtLink>
              </div>
            </div>
          </Transition>
        </div>
      </nav>

      <!-- CTA button -->
      <div class="hidden lg:block">
        <NuxtLink to="/aanvraag" class="stitch-button">
          Start aanvraag
        </NuxtLink>
      </div>

      <!-- Mobile menu button -->
      <button
        class="inline-flex h-12 w-12 items-center justify-center rounded-xl border transition-all duration-200 lg:hidden"
        :style="{
          borderColor: isOpen ? 'var(--accent)' : 'var(--color-line-strong)',
          color: isOpen ? 'var(--accent-strong)' : 'var(--color-ink)',
          background: isOpen ? 'var(--accent-soft)' : 'transparent'
        }"
        @click="isOpen = !isOpen"
        :aria-expanded="isOpen"
        aria-label="Menu openen"
      >
        <span class="text-xl font-semibold">{{ isOpen ? '×' : '☰' }}</span>
      </button>
    </div>

    <!-- Mobile menu -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="isOpen"
        class="border-t lg:hidden"
        :style="{ borderColor: 'var(--color-line)', background: 'var(--color-bg)' }"
      >
        <div class="hero-inner flex flex-col gap-3 py-6">
          <NuxtLink
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="rounded-xl border px-5 py-4 font-display text-base font-medium transition-all duration-200"
            :style="{
              borderColor: route.path === link.to ? 'var(--accent)' : 'var(--color-line)',
              color: route.path === link.to ? 'var(--accent-strong)' : 'var(--color-ink)',
              background: route.path === link.to ? 'var(--accent-soft)' : 'var(--color-surface)'
            }"
          >
            {{ link.label }}
          </NuxtLink>
          <NuxtLink to="/aanvraag" class="stitch-button mt-3 w-full py-4 text-base">
            Start aanvraag
          </NuxtLink>
        </div>
      </div>
    </Transition>
  </header>
</template>
