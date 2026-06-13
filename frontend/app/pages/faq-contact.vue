<script setup lang="ts">
const offer = useOffer();

useSeoMeta({
  title: 'FAQ & contact',
  description: 'Veelgestelde vragen over persoonlijke nummers van Voor Ieder Moment, plus contactgegevens.',
});

const faqs = [
  { question: 'Zijn de nummers met AI gemaakt?', answer: 'Ja. We combineren kant-en-klare, deels al gemaakte rijmende bouwstenen met AI die de tekst personaliseert op basis van jouw verhaal. Daardoor is het snel én betaalbaar.' },
  { question: 'Wat krijg ik precies?', answer: 'Eén afgewerkte versie van je nummer. Diezelfde ene versie deel je overal — op Spotify, Apple Music en social media.' },
  { question: 'Wat kost een nummer?', answer: offer.saleOn.value ? `Normaal ${offer.formattedRegular}, nu in de aanbieding ${offer.formattedSale} per nummer.` : `${offer.formattedRegular} per nummer.` },
  { question: 'Hoe snel heb ik mijn nummer?', answer: 'Doorgaans binnen enkele dagen. Heb je een specifieke datum (zoals een feest of Vaderdag), vraag het dan ruim van tevoren aan.' },
  { question: 'Sommige nummers staan al op Spotify — hoe zit dat?', answer: 'Voor populaire momenten zoals geslaagd, rijbewijs en eigen huis hebben we al een nummer met heel veel namen. Staat jouw naam er niet bij, dan maken we op aanvraag een eigen versie.' },
  { question: 'Kan ik het voor iemand anders aanvragen?', answer: 'Zeker — vul gewoon de naam van diegene in. Ideaal als verrassingscadeau.' },
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
          <h2 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">Neem contact op</h2>
          <form class="mt-6 space-y-4" @submit.prevent>
            <div>
              <label class="field-label" for="c-name">Naam</label>
              <input id="c-name" class="field-input" placeholder="Je naam" />
            </div>
            <div>
              <label class="field-label" for="c-email">E-mailadres</label>
              <input id="c-email" type="email" class="field-input" placeholder="naam@voorbeeld.nl" />
            </div>
            <div>
              <label class="field-label" for="c-msg">Bericht</label>
              <textarea id="c-msg" rows="4" class="field-input" placeholder="Waar kunnen we je mee helpen?" />
            </div>
            <button type="submit" class="stitch-button w-full py-3.5">Verstuur bericht</button>
          </form>

          <div class="mt-8 space-y-2 border-t pt-6 text-sm" :style="{ borderColor: 'var(--color-line)', color: 'var(--color-ink-soft)' }">
            <p><a class="underline" href="mailto:info@vooriedermoment.nl">info@vooriedermoment.nl</a></p>
            <p>Amsterdam, Nederland</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
