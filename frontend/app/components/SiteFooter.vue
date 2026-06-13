<script setup lang="ts">
import { consumerCategories, categoryPath } from '~/data/categories';

const year = new Date().getFullYear();
</script>

<template>
  <footer class="mt-16 border-t" :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }">
    <div class="site-container py-14">
      <div class="grid gap-10 text-sm md:grid-cols-[1.3fr_1fr_1fr]">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <span class="logo-card"><img src="/logowit.png" alt="" class="h-5 w-auto" /></span>
            <span class="font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">Voor Ieder Moment</span>
          </div>
          <p class="max-w-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">
            Een persoonlijk nummer voor elk moment. Snel, betaalbaar en overal te beluisteren op
            Spotify en Apple Music.
          </p>
          <p class="text-xs" :style="{ color: 'var(--color-ink-faint)' }">
            Nummers worden deels uit kant-en-klare bouwstenen samengesteld en met AI gepersonaliseerd.
          </p>
        </div>

        <div>
          <p class="mb-4 text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">Momenten</p>
          <div class="flex flex-col gap-2.5" :style="{ color: 'var(--color-ink-soft)' }">
            <NuxtLink v-for="c in consumerCategories" :key="c.slug" :to="categoryPath(c)" class="transition hover:underline">
              {{ c.title }}
            </NuxtLink>
          </div>
        </div>

        <div>
          <p class="mb-4 text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">Info</p>
          <div class="flex flex-col gap-2.5" :style="{ color: 'var(--color-ink-soft)' }">
            <NuxtLink to="/aanvraag" class="transition hover:underline">Aanvraag</NuxtLink>
            <NuxtLink to="/zakelijk/bouwbedrijven" class="transition hover:underline">Voor bedrijven</NuxtLink>
            <NuxtLink to="/over-ons" class="transition hover:underline">Over ons</NuxtLink>
            <NuxtLink to="/faq-contact" class="transition hover:underline">FAQ & contact</NuxtLink>
            <a href="mailto:info@vooriedermoment.nl" class="transition hover:underline">info@vooriedermoment.nl</a>
          </div>
        </div>
      </div>

      <div class="soft-divider mt-10 mb-6" />
      <div class="flex flex-col gap-3 text-xs sm:flex-row sm:items-center sm:justify-between" :style="{ color: 'var(--color-ink-faint)' }">
        <p>© {{ year }} Voor Ieder Moment</p>
        <p>Jouw verhaal, jouw nummer.</p>
      </div>
    </div>
  </footer>
</template>
