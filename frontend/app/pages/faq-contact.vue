<script setup lang="ts">
const offer = useOffer();

useSeoMeta({
  title: 'FAQ & contact',
  description: 'Veelgestelde vragen over persoonlijke nummers van Voor Ieder Moment, plus contactgegevens.',
});

const faqs = [
  { question: 'Worden de nummers met AI gemaakt?', answer: 'Ja. AI helpt ons verschillende teksten en muzikale richtingen te maken. We combineren dat met vaste songstructuren, rijmende bouwstenen en de persoonlijke details uit jouw briefing. Zo blijft het betaalbaar én herkenbaar.' },
  { question: 'Wat krijg ik precies?', answer: 'Je ontvangt eerst vier unieke samples van 15 seconden. Jij kiest je favoriet; daarna maken wij één complete versie en regelen we de release op Spotify en Apple Music.' },
  { question: 'Wat kost een nummer?', answer: offer.hasDiscount.value ? `Normaal ${offer.formattedRegular}, nu ${offer.formattedSale}. Vier samples en de release zijn inbegrepen.` : `${offer.formattedRegular}. Vier samples en de release op Spotify en Apple Music zijn inbegrepen.` },
  { question: 'Hoe snel heb ik mijn nummer?', answer: 'Binnen 24–72 uur na betaling ontvang je vier samples. Na jouw keuze duurt het nog 24–72 uur voordat de complete versie op Spotify en Apple Music staat. Heb je een vaste datum? Vraag dan ruim op tijd aan.' },
  { question: 'Sommige nummers staan al op Spotify — hoe zit dat?', answer: 'Voor populaire momenten zoals geslaagd, rijbewijs en eigen huis hebben we al een nummer met heel veel namen. Staat jouw naam er niet bij, dan maken we op aanvraag een eigen versie.' },
  { question: 'Kan ik het voor iemand anders aanvragen?', answer: 'Zeker. Vul de naam en details van diegene in en houd de verrassing geheim tot het grote moment.' },
];

useJsonLd(faqSchema(faqs));
</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <header v-reveal class="mx-auto max-w-3xl text-center">
      <span class="section-kicker">FAQ &amp; contact</span>
      <h1 class="section-heading text-4xl md:text-6xl">Vragen &amp; contact</h1>
      <p class="section-subtext mx-auto mt-5 max-w-2xl">
        Alles wat je wilt weten over je persoonlijke nummer. Staat je vraag er niet bij? Mail ons gerust.
      </p>
    </header>

    <div class="mt-12 grid gap-12 lg:grid-cols-[1.1fr_0.9fr]">
      <section>
        <div class="space-y-3">
          <details v-for="faq in faqs" :key="faq.question" v-reveal class="rich-card p-6">
            <summary class="cursor-pointer list-none font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">
              {{ faq.question }}
            </summary>
            <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ faq.answer }}</p>
          </details>
        </div>
      </section>

      <section v-reveal>
        <div class="rich-card p-8">
          <span class="section-kicker">Persoonlijk contact</span>
          <h2 class="mt-4 font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">
            Staat je vraag er niet tussen?
          </h2>
          <p class="mt-4 leading-7" :style="{ color: 'var(--color-ink-soft)' }">
            Mail ons gerust over je aanvraag, een vaste deadline of een moment dat nog niet op de site staat.
            Heb je al besteld? Vermeld dan het e-mailadres van je bestelling.
          </p>
          <a href="mailto:info@vooriedermoment.nl" class="stitch-button mt-7 w-full py-3.5 text-center">
            Mail info@vooriedermoment.nl
          </a>

          <div class="mt-8 space-y-2 border-t pt-6 text-sm" :style="{ borderColor: 'var(--color-line)', color: 'var(--color-ink-soft)' }">
            <p>Voor Ieder Moment</p>
            <p>Amsterdam, Nederland</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
