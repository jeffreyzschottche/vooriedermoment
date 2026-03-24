<script setup lang="ts">
const route = useRoute();

const isOpen = ref(false);

const links = [
  { to: '/', label: 'Home' },
  { to: '/over-ons', label: 'Over Ons' },
  { to: '/aanvraag', label: 'Aanvraag' },
  { to: '/faq-contact', label: 'FAQ & Contact' },
  { to: '/checkout', label: 'Checkout' },
];

watch(
  () => route.path,
  () => {
    isOpen.value = false;
  },
);
</script>

<template>
  <header class="sticky top-0 z-50 border-b border-[rgba(77,70,53,0.18)] bg-[rgba(19,19,19,0.86)] backdrop-blur-xl shadow-[0_8px_40px_rgba(0,0,0,0.22)]">
    <div class="site-container flex min-h-16 items-center justify-between gap-4 py-3 lg:min-h-[4.75rem]">
      <NuxtLink to="/" class="flex min-w-0 items-center gap-3">
        <span class="logo-card">
          <img src="/logozwart.png" alt="Voor Ieder Moment" class="h-5 w-auto sm:h-6" />
        </span>
        <span class="truncate font-display text-xs font-bold uppercase tracking-[0.1em] text-[var(--color-primary)] sm:text-sm">
          Voor Ieder Moment
        </span>
      </NuxtLink>

      <nav class="hidden items-center gap-2 rounded-full border border-[rgba(77,70,53,0.22)] bg-white/[0.03] p-2 lg:flex">
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          :class="[
            'rounded-full px-4 py-2 text-sm font-medium transition',
            route.path === link.to
              ? 'bg-[rgba(242,202,80,0.12)] text-[var(--color-primary)]'
              : 'text-[var(--color-on-surface-muted)] hover:bg-white/[0.04] hover:text-white'
          ]"
        >
          {{ link.label }}
        </NuxtLink>
      </nav>

      <div class="hidden items-center gap-3 lg:flex">
        <NuxtLink
          to="/aanvraag"
          class="stitch-button"
        >
          Start Aanvraag
        </NuxtLink>
      </div>

      <button
        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/[0.03] text-[var(--color-primary)] lg:hidden"
        @click="isOpen = !isOpen"
        aria-label="Menu openen"
      >
        <span class="text-xl">{{ isOpen ? '×' : '☰' }}</span>
      </button>
    </div>

    <div v-if="isOpen" class="border-t border-white/5 bg-[rgba(12,12,12,0.97)] lg:hidden">
      <div class="site-container flex flex-col gap-3 py-5">
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          :class="[
            'rounded-2xl border px-4 py-4 font-display text-base',
            route.path === link.to
              ? 'border-[rgba(242,202,80,0.22)] bg-[rgba(242,202,80,0.08)] text-[var(--color-primary)]'
              : 'border-white/5 bg-white/5 text-[var(--color-on-surface)]'
          ]"
        >
          {{ link.label }}
        </NuxtLink>

        <NuxtLink
          to="/aanvraag"
          class="stitch-button mt-2 w-full"
        >
          Start Aanvraag
        </NuxtLink>
      </div>
    </div>
  </header>
</template>
