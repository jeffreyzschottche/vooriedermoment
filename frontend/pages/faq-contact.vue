<script setup lang="ts">
useSeoMeta({
  title: 'FAQ & Contact',
  description: 'Veelgestelde vragen en contactmogelijkheden voor Voor Ieder Moment.',
});

const faqs = [
  {
    question: 'Hoe verloopt het proces van een aanvraag?',
    answer:
      'Na uw aanvraag plannen we een persoonlijk gesprek. We luisteren naar uw verhaal, de emoties en de details die u wilt verwerken. Onze componisten gaan vervolgens aan de slag om een uniek muziekstuk te creëren dat perfect aansluit bij uw moment.',
  },
  {
    question: 'Kan het lied op Spotify worden uitgebracht?',
    answer:
      'Zeker. Wij verzorgen de volledige distributie naar alle grote streamingplatforms, inclusief Spotify en Apple Music. Zo kunt u uw persoonlijke herinnering altijd en overal delen met uw dierbaren.',
  },
  {
    question: 'Wat zijn de betalingsmogelijkheden?',
    answer:
      'U kunt veilig betalen via iDEAL, creditcard of bankoverschrijving. Voor grotere projecten bieden we de mogelijkheid om in termijnen te betalen. Transparantie staat bij ons voorop; u ontvangt vooraf een duidelijke offerte.',
  },
  {
    question: 'Hoe lang duurt de productie gemiddeld?',
    answer:
      'Gemiddeld duurt het proces van intake tot het definitieve resultaat 4 tot 6 weken. Voor spoedaanvragen hebben we een versneld proces beschikbaar.',
  },
];
</script>

<template>
  <div class="px-5 py-14 sm:px-8 sm:py-16">
    <div class="mx-auto max-w-7xl">
      <header class="mb-16 text-center">
        <h1 class="section-heading text-5xl md:text-7xl">Vragen & Contact</h1>
        <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-[var(--color-on-surface-muted)]">
          Alles wat u moet weten over uw persoonlijke muzikale herinnering en hoe wij samen uw verhaal vertalen naar muziek.
        </p>
      </header>

      <div class="grid gap-14 lg:grid-cols-[1.15fr_0.85fr]">
        <section>
          <div class="mb-6 text-[10px] font-bold uppercase tracking-[0.22em] text-[var(--color-primary)]">Veelgestelde vragen</div>
          <div class="space-y-4">
            <article v-for="faq in faqs" :key="faq.question" class="rich-card p-6 sm:p-7">
              <h2 class="flex items-center justify-between font-display text-2xl text-[var(--color-primary)]">
                {{ faq.question }}
                <span class="text-base text-[var(--color-on-surface-muted)]">⌄</span>
              </h2>
              <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">{{ faq.answer }}</p>
            </article>
          </div>
        </section>

        <section>
          <div class="rich-card bg-[#2a2a2a] p-8">
            <h2 class="section-heading text-4xl">Neem Contact Op</h2>
            <form class="mt-8 space-y-5">
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.22em] text-[var(--color-on-surface-muted)]">Volledige naam</label>
                <input class="w-full rounded-2xl border-none bg-[#111] px-4 py-4 text-sm text-white outline-none" placeholder="Uw naam" />
              </div>
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.22em] text-[var(--color-on-surface-muted)]">E-mailadres</label>
                <input class="w-full rounded-2xl border-none bg-[#111] px-4 py-4 text-sm text-white outline-none" placeholder="naam@voorbeeld.nl" />
              </div>
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.22em] text-[var(--color-on-surface-muted)]">Uw bericht</label>
                <textarea rows="4" class="w-full rounded-[1.5rem] border-none bg-[#111] px-4 py-4 text-sm text-white outline-none" placeholder="Waar kunnen we u mee helpen?" />
              </div>
              <button type="button" class="stitch-button flex w-full py-3.5">Verstuur Bericht</button>
            </form>

            <div class="mt-10 space-y-5 border-t border-[rgba(77,70,53,0.18)] pt-8 text-sm text-[var(--color-on-surface-muted)]">
              <p>info@vooriedermoment.nl</p>
              <p>+31 (0)20 123 4567</p>
              <p>Amsterdam, Nederland</p>
            </div>
          </div>
        </section>
      </div>

      <section class="mt-28 border-t border-[rgba(77,70,53,0.15)] pt-20 text-center">
        <p class="mb-6 text-5xl text-[var(--color-primary)]">”</p>
        <blockquote class="mx-auto max-w-4xl font-display text-3xl italic leading-tight text-white md:text-5xl">
          Muziek geeft een stem aan emoties waar woorden alleen niet genoeg zijn. Wij helpen u die stem te vinden.
        </blockquote>
      </section>
    </div>
  </div>
</template>
