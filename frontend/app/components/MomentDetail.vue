<script setup lang="ts">
import type { Category } from '~/data/categories';
import { categoryPath, themeVars, categoryImage } from '~/data/categories';

const props = defineProps<{ category: Category }>();

const cover = computed(() => categoryImage(props.category));

// Zet de accent-variabelen voor deze pagina (per-categorie huisstijl).
const themeStyle = computed(() => themeVars(props.category.theme));

const requestHref = computed(() => `${categoryPath(props.category)}#aanvraag`);
const isExisting = computed(() => props.category.variant === 'existing');
const isB2b = computed(() => props.category.variant === 'b2b');

const productionHighlights = [
  { title: 'Context ophalen', text: 'Het formulier vraagt per categorie naar namen, momenten, sfeer en details die in de tekst moeten.' },
  { title: 'Lyrics bouwen', text: 'We combineren herbruikbare rijmblokken met persoonlijke regels uit jouw verhaal.' },
  { title: 'Muziekprompt maken', text: 'Na betaling gaat de definitieve tekst samen met stijl, stem en tempo door naar de muziekstap.' },
];
</script>

<template>
  <div class="moment-theme" :style="themeStyle">
    <!-- HERO -->
    <section class="site-container py-16 sm:py-24">
      <div class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
          <span v-reveal class="section-kicker">{{ category.kicker }}</span>
          <h1 v-reveal class="section-heading text-4xl leading-tight md:text-6xl">{{ category.heroTitle }}</h1>
          <p v-reveal class="mt-6 max-w-xl text-lg leading-8" :style="{ color: 'var(--color-ink-soft)' }">
            {{ category.heroLead }}
          </p>
          <div v-reveal class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center">
            <a href="#aanvraag" class="stitch-button px-8 py-3.5">Vraag jouw versie aan</a>
            <OfferBadge size="sm" inline />
          </div>
        </div>

        <div v-reveal class="space-y-5">
          <div
            class="overflow-hidden rounded-2xl border"
            :style="{ borderColor: 'var(--color-line)', boxShadow: '0 18px 48px rgba(13,21,18,0.14)' }"
          >
            <img
              :src="cover"
              :alt="`${category.title} — persoonlijk nummer`"
              class="aspect-[16/9] w-full object-cover"
            />
          </div>

          <div class="rich-card p-7 sm:p-8">
            <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">Wat je krijgt</h2>
            <ul class="mt-5 space-y-3 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
              <li v-for="item in category.whatYouGet" :key="item" class="flex gap-3">
                <span :style="{ color: 'var(--accent-strong)' }" aria-hidden="true">✓</span>{{ item }}
              </li>
            </ul>
            <p class="mt-6 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ category.intro }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- EXISTING: playlist + zoeken -->
    <section v-if="isExisting" class="py-14" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container space-y-8">
        <div v-reveal>
          <PlaylistShowcase
            :embed="category.playlistEmbed"
            :title="category.sampleTracks?.[0]?.title ?? category.title"
            :subtitle="category.intro"
          />
        </div>
        <div v-reveal>
          <SongSearch :hook="category.existingHook" :request-href="requestHref" />
        </div>
      </div>
    </section>

    <!-- B2B: sample-nummers -->
    <section v-if="isB2b && category.sampleTracks?.length" class="py-14" :style="{ background: 'var(--color-surface-soft)' }">
      <div class="site-container">
        <div v-reveal class="mb-8 text-center">
          <span class="section-kicker">Voorbeelden</span>
          <h2 class="section-heading text-3xl md:text-4xl">Zo kan jouw bedrijfsnummer klinken</h2>
          <p class="section-subtext mx-auto mt-3 max-w-2xl">Vier richtingen als voorproefje. Jouw nummer wordt volledig op jullie bedrijf afgestemd.</p>
        </div>
        <div v-reveal>
          <SampleTrackList :tracks="category.sampleTracks" :columns="4" />
        </div>
      </div>
    </section>

    <!-- AANVRAAG -->
    <section class="site-container py-16 sm:py-20">
      <div class="grid gap-5 md:grid-cols-3">
        <article v-for="item in productionHighlights" :key="item.title" v-reveal class="rich-card p-6">
          <div class="mb-5 h-1.5 w-14 accent-gradient" style="border-radius: 999px;" />
          <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.title }}</h2>
          <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.text }}</p>
        </article>
      </div>
    </section>

    <section id="aanvraag" class="site-container pb-16 sm:pb-20">
      <div class="mx-auto max-w-5xl">
        <div v-reveal class="mb-8 text-center">
          <span class="section-kicker">Aanvragen</span>
          <h2 class="section-heading text-3xl md:text-4xl">{{ isB2b ? 'Vraag jullie bedrijfsnummer aan' : 'Vraag jouw nummer aan' }}</h2>
          <p class="section-subtext mx-auto mt-3 max-w-2xl">
            Dit formulier is afgestemd op {{ category.title.toLowerCase() }}. De antwoorden worden gebruikt
            voor de lyrics én voor de muziekprompt na betaling.
          </p>
        </div>
        <div v-reveal>
          <IntakeForm :category="category" />
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section v-if="category.faq.length" class="site-container pb-20">
      <div class="mx-auto max-w-3xl">
        <h2 v-reveal class="section-heading mb-6 text-center text-2xl md:text-3xl">Veelgestelde vragen</h2>
        <div class="space-y-3">
          <details v-for="item in category.faq" :key="item.question" v-reveal class="rich-card p-6">
            <summary class="cursor-pointer list-none font-semibold" :style="{ color: 'var(--color-ink)' }">{{ item.question }}</summary>
            <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ item.answer }}</p>
          </details>
        </div>
      </div>
    </section>
  </div>
</template>
