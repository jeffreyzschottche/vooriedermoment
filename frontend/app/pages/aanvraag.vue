<script setup lang="ts">
import {
  musicStyleOptions,
  requestCategories,
  tempoOptions,
  toneOptions,
  vocalOptions,
  type Category,
} from '~/data/categories';

useSeoMeta({
  title: 'Vraag een nummer aan',
  description: 'Kies je moment en vertel je verhaal. Wij maken er een persoonlijk nummer van.',
});

// Generiek "ander moment" — staat niet in de nav, alleen hier als vangnet.
const otherCategory: Category = {
  slug: 'anders',
  title: 'Een ander moment',
  navLabel: 'Anders',
  emoji: '✨',
  variant: 'standard',
  theme: { accent: '#ea6848', accentStrong: '#b63d26', accentSoft: '#ffe4dc', accentInk: '#ffffff' },
  kicker: 'Jouw eigen gelegenheid',
  heroTitle: 'Een nummer voor jouw moment',
  heroLead: '',
  intro: '',
  whatYouGet: [],
  intakeFields: [
    { name: 'occasion', label: 'Voor welke gelegenheid?', type: 'text', placeholder: 'Bijv. pensioen, jubileum, afscheid', required: true, span: 'half' },
    { name: 'recipientName', label: 'Voor wie is het nummer?', type: 'text', placeholder: 'Naam', required: true, span: 'half', help: 'Vul hier één hoofdnaam in. Extra namen kun je met het plusje toevoegen.' },
    { name: 'fromName', label: 'Van wie komt het nummer?', type: 'text', placeholder: 'Bijv. het hele team', span: 'half' },
    { name: 'tone', label: 'Sfeer / toon', type: 'select', required: true, span: 'half', options: toneOptions },
    { name: 'vocals', label: 'Stem', type: 'select', span: 'half', options: vocalOptions },
    { name: 'musicStyle', label: 'Genre kiezen', type: 'select', span: 'half', options: musicStyleOptions },
    { name: 'tempo', label: 'Snelheid / tempo', type: 'select', span: 'half', options: tempoOptions },
    { name: 'anecdotes', label: 'Verhaal, anekdotes & inside jokes', type: 'textarea', required: true, span: 'full', placeholder: 'Vertel wat dit moment uniek maakt — namen, plaatsen, grapjes...', help: 'Hoe concreter, hoe persoonlijker het nummer.' },
    { name: 'mustMention', label: 'Wat moet er absoluut in?', type: 'textarea', span: 'full', placeholder: 'Namen, zinnen, plekken of gebeurtenissen die niet mogen ontbreken.' },
    { name: 'avoid', label: 'Wat moeten we vermijden?', type: 'text', span: 'full', placeholder: 'Bijv. te sentimenteel, bepaalde namen, grove grappen...' },
    { name: 'email', label: 'Jouw e-mailadres', type: 'email', placeholder: 'naam@voorbeeld.nl', required: true, span: 'full', help: 'Hier ontvang je de samples en updates.' },
  ],
  faq: [],
  seoTitle: '',
  seoDescription: '',
};
</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <header v-reveal class="site-frame grid gap-8 p-7 sm:p-10 lg:grid-cols-[1fr_0.8fr] lg:items-center">
      <div>
        <span class="section-kicker">Aanvragen</span>
        <h1 class="section-heading text-4xl md:text-6xl">Kies het moment dat je wilt vereeuwigen</h1>
        <p class="section-subtext mt-5 max-w-2xl">
          Elk verhaal vraagt om andere details. Daarom krijg je vragen die passen bij jouw moment,
          van herinneringen en inside jokes tot een slogan, clubcultuur of bijzondere gebeurtenis.
        </p>
      </div>
      <div class="grid gap-3 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 1:</strong> kies jouw moment</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 2:</strong> vertel wat het persoonlijk maakt</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 3:</strong> ontvang vier samples</div>
      </div>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in requestCategories" :key="c.slug" :category="c" />
    </div>

    <div class="mx-auto mt-16 max-w-3xl">
      <div v-reveal class="mb-6 text-center">
        <h2 class="section-heading text-2xl md:text-3xl">Of: een ander moment</h2>
        <p class="section-subtext mt-2">Pensioen, jubileum, afscheid, of iets heel anders.</p>
      </div>
      <div v-reveal>
        <IntakeForm :category="otherCategory" />
      </div>
    </div>
  </div>
</template>
