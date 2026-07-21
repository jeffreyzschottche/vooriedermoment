<script setup lang="ts">
import { requestCategories, categoryPath } from '~/data/categories';

const year = new Date().getFullYear();
</script>

<template>
  <footer class="mt-24 sm:mt-32">
    <!-- Full-width dark footer -->
    <div class="studio-shell-full">
      <div class="hero-inner py-16 sm:py-20">
        <div class="grid gap-14 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
          <!-- Left: CTA -->
          <div v-reveal>
            <div class="flex items-center gap-4">
              <img src="/logowit.png" alt="" class="h-12 w-auto" />
              <span class="font-display text-xl font-semibold text-white sm:text-2xl">
                Voor Ieder Moment
              </span>
            </div>

            <h2 class="mt-10 max-w-xl font-display text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
              Klaar om van een verhaal een nummer te maken?
            </h2>

            <p class="mt-5 max-w-lg text-base leading-relaxed text-white/55 sm:text-lg">
              Vertel wat jouw moment bijzonder maakt en ontvang vier unieke samples.
              Kies je favoriet; wij regelen de release op Spotify en Apple Music.
            </p>

            <div class="mt-10 flex flex-col gap-4 sm:flex-row">
              <NuxtLink to="/aanvraag" class="stitch-button px-10 py-4 text-base">
                Start aanvraag
              </NuxtLink>
              <NuxtLink to="/momenten" class="stitch-outline-button border-white/15 bg-white/5 px-10 py-4 text-base text-white hover:bg-white hover:text-[var(--color-ink)]">
                Alle momenten
              </NuxtLink>
            </div>
          </div>

          <!-- Right: Links -->
          <div v-reveal data-reveal-delay="0.15" class="grid gap-10 text-sm sm:grid-cols-2 lg:pt-4">
            <div>
              <p class="mb-5 text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--accent-gold);">
                Momenten
              </p>
              <nav class="grid gap-3 text-white/55">
                <NuxtLink
                  v-for="c in requestCategories.slice(0, 6)"
                  :key="c.slug"
                  :to="categoryPath(c)"
                  class="transition-colors duration-200 hover:text-white"
                >
                  {{ c.title }}
                </NuxtLink>
              </nav>
            </div>

            <div>
              <p class="mb-5 text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--accent-gold);">
                Info
              </p>
              <nav class="grid gap-3 text-white/55">
                <NuxtLink to="/aanvraag" class="transition-colors duration-200 hover:text-white">
                  Aanvraag
                </NuxtLink>
                <NuxtLink to="/momenten" class="transition-colors duration-200 hover:text-white">
                  Alle momenten
                </NuxtLink>
                <NuxtLink to="/over-ons" class="transition-colors duration-200 hover:text-white">
                  Over ons
                </NuxtLink>
                <NuxtLink to="/faq-contact" class="transition-colors duration-200 hover:text-white">
                  FAQ & contact
                </NuxtLink>
                <a href="mailto:info@vooriedermoment.nl" class="transition-colors duration-200 hover:text-white">
                  info@vooriedermoment.nl
                </a>
              </nav>
            </div>
          </div>
        </div>

        <!-- Bottom bar -->
        <div class="mt-16 flex flex-col gap-4 border-t border-white/10 pt-8 text-sm text-white/35 sm:flex-row sm:items-center sm:justify-between">
          <p>&copy; {{ year }} Voor Ieder Moment. Alle rechten voorbehouden.</p>
          <p>Persoonlijke nummers op Spotify &amp; Apple Music.</p>
        </div>
      </div>
    </div>
  </footer>
</template>
