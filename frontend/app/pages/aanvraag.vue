<script setup lang="ts">
import { consumerCategories, type Category } from '~/data/categories';

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
  theme: { accent: '#e07a2c', accentStrong: '#b85f17', accentSoft: '#fcecd9', accentInk: '#ffffff' },
  kicker: 'Jouw eigen gelegenheid',
  heroTitle: 'Een nummer voor jouw moment',
  heroLead: '',
  intro: '',
  whatYouGet: [],
  intakeFields: [
    { name: 'occasion', label: 'Voor welke gelegenheid?', type: 'text', placeholder: 'Bijv. pensioen, jubileum, afscheid', required: true, span: 'half' },
    { name: 'recipientName', label: 'Voor wie is het?', type: 'text', placeholder: 'Naam', required: true, span: 'half' },
    { name: 'fromName', label: 'Van wie is het?', type: 'text', placeholder: 'Bijv. het hele team', span: 'half' },
    { name: 'tone', label: 'Sfeer / toon', type: 'select', required: true, span: 'half', options: ['Vrolijk & uptempo', 'Emotioneel & ontroerend', 'Grappig & ad rem', 'Stoer & energiek'] },
    { name: 'vocals', label: 'Stem', type: 'select', span: 'half', options: ['Mannenstem', 'Vrouwenstem', 'Duet', 'Maakt niet uit'] },
    { name: 'anecdotes', label: 'Verhaal, anekdotes & inside jokes', type: 'textarea', required: true, span: 'full', placeholder: 'Vertel wat dit moment uniek maakt — namen, plaatsen, grapjes...', help: 'Hoe concreter, hoe persoonlijker het nummer.' },
    { name: 'email', label: 'Jouw e-mailadres', type: 'email', placeholder: 'naam@voorbeeld.nl', required: true, span: 'full', help: 'Hier leveren we het nummer af.' },
  ],
  faq: [],
  seoTitle: '',
  seoDescription: '',
};
</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <header v-reveal class="mx-auto max-w-3xl text-center">
      <span class="section-kicker">Aanvragen</span>
      <h1 class="section-heading text-4xl md:text-6xl">Voor welk moment maken we het?</h1>
      <p class="section-subtext mx-auto mt-5 max-w-2xl">
        Kies een moment voor een formulier op maat — of vul hieronder direct het algemene formulier in.
      </p>
    </header>

    <div v-reveal class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <MomentCard v-for="c in consumerCategories" :key="c.slug" :category="c" />
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
