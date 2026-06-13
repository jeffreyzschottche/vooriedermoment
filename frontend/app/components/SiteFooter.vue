<script setup lang="ts">
import { requestCategories, categoryPath } from '~/data/categories';

const year = new Date().getFullYear();
</script>

<template>
  <footer class="mt-20">
    <div class="wide-container pb-6">
      <div class="studio-shell p-6 sm:p-10">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
          <div>
            <div class="flex items-center gap-3">
              <span class="logo-card"><img src="/logowit.png" alt="" class="h-5 w-auto" /></span>
              <span class="font-display text-xl font-semibold text-white">Voor Ieder Moment</span>
            </div>
            <h2 class="mt-7 max-w-2xl font-display text-4xl font-semibold leading-[1.03] text-white sm:text-5xl">
              Klaar om van een verhaal een nummer te maken?
            </h2>
            <p class="mt-4 max-w-xl text-sm leading-7 text-white/[0.62]">
              Kies een moment, vul een gerichte briefing in en laat de productieflow lyrics
              en een muziekprompt klaarzetten.
            </p>
            <NuxtLink to="/aanvraag" class="stitch-button mt-7 px-8 py-3.5">Start aanvraag</NuxtLink>
          </div>

          <div class="grid gap-8 text-sm sm:grid-cols-2">
            <div>
              <p class="mb-4 text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--accent-gold);">Momenten</p>
              <div class="grid gap-2.5 text-white/[0.62]">
                <NuxtLink v-for="c in requestCategories.slice(0, 6)" :key="c.slug" :to="categoryPath(c)" class="transition hover:text-white">
                  {{ c.title }}
                </NuxtLink>
              </div>
            </div>

            <div>
              <p class="mb-4 text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--accent-gold);">Info</p>
              <div class="grid gap-2.5 text-white/[0.62]">
                <NuxtLink to="/aanvraag" class="transition hover:text-white">Aanvraag</NuxtLink>
                <NuxtLink to="/momenten" class="transition hover:text-white">Alle momenten</NuxtLink>
                <NuxtLink to="/over-ons" class="transition hover:text-white">Over ons</NuxtLink>
                <NuxtLink to="/faq-contact" class="transition hover:text-white">FAQ & contact</NuxtLink>
                <a href="mailto:info@vooriedermoment.nl" class="transition hover:text-white">info@vooriedermoment.nl</a>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-6 text-xs text-white/[0.42] sm:flex sm:items-center sm:justify-between">
          <p>© {{ year }} Voor Ieder Moment</p>
          <p class="mt-2 sm:mt-0">Lyrics, muziekprompt en productieflow op maat.</p>
        </div>
      </div>
    </div>
  </footer>
</template>
