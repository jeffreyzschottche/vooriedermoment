<script setup lang="ts">
import { requestCategories, type Category } from '~/data/categories';

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
    { name: 'recipientName', label: 'Voor wie is het?', type: 'text', placeholder: 'Naam', required: true, span: 'half' },
    { name: 'fromName', label: 'Van wie is het?', type: 'text', placeholder: 'Bijv. het hele team', span: 'half' },
    { name: 'tone', label: 'Sfeer / toon', type: 'select', required: true, span: 'half', options: ['Vrolijk & uptempo', 'Emotioneel maar niet zwaar', 'Grappig & ad rem', 'Stoer & energiek', 'Warm & persoonlijk'] },
    { name: 'vocals', label: 'Stem', type: 'select', span: 'half', options: ['Mannenstem', 'Vrouwenstem', 'Duet', 'Maakt niet uit'] },
    { name: 'musicStyle', label: 'Muzikale richting', type: 'select', span: 'half', options: ['Nederlandstalige pop', 'Feest / meezinger', 'Akoestisch en klein', 'Rock / anthem', 'Urban pop', 'Laat ons kiezen'] },
    { name: 'anecdotes', label: 'Verhaal, anekdotes & inside jokes', type: 'textarea', required: true, span: 'full', placeholder: 'Vertel wat dit moment uniek maakt — namen, plaatsen, grapjes...', help: 'Hoe concreter, hoe persoonlijker het nummer.' },
    { name: 'mustMention', label: 'Wat moet er absoluut in?', type: 'textarea', span: 'full', placeholder: 'Namen, zinnen, plekken of gebeurtenissen die niet mogen ontbreken.' },
    { name: 'avoid', label: 'Wat moeten we vermijden?', type: 'text', span: 'full', placeholder: 'Bijv. te sentimenteel, bepaalde namen, grove grappen...' },
    { name: 'email', label: 'Jouw e-mailadres', type: 'email', placeholder: 'naam@voorbeeld.nl', required: true, span: 'full', help: 'Hier leveren we het nummer af.' },
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
        <h1 class="section-heading text-4xl md:text-6xl">Kies eerst het juiste type nummer</h1>
        <p class="section-subtext mt-5 max-w-2xl">
          Elk moment heeft een eigen formulier. Zo vragen we bij een bouwbedrijf naar slogan en projecten,
          bij een teamlied naar clubcultuur, en bij een cadeau naar herinneringen en inside jokes.
        </p>
      </div>
      <div class="grid gap-3 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 1:</strong> context verzamelen</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 2:</strong> lyrics + rijmblokken genereren</div>
        <div class="metric-tile"><strong :style="{ color: 'var(--color-ink)' }">Stap 3:</strong> muziekprompt na betaling</div>
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
