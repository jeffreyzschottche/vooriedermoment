<script setup lang="ts">
const offer = useOffer();

useSeoMeta({
  title: 'FAQ & contact',
  description: 'Veelgestelde vragen over persoonlijke nummers van Voor Ieder Moment, plus contactgegevens.',
});

const faqs = [
  { question: 'Hoe werkt het van aanvraag tot Spotify?', answer: 'Kies eerst het moment waarvoor je een nummer wilt laten maken. Daarna ontvang je een formulier dat je stap voor stap invult met namen, herinneringen, inside jokes en de gewenste sfeer. Na betaling maken we verschillende korte muzikale opties. Via een persoonlijke link kies je jouw favoriet. Wij werken die keuze uit tot een compleet nummer en publiceren het voor je op Spotify en Apple Music. Zodra het live staat, ontvang je de luisterlinks per e-mail.' },
  { question: 'Hoe vul ik het formulier in?', answer: 'Het formulier leidt je stap voor stap door de aanvraag. Je vertelt voor wie het nummer is, wat de gelegenheid is, welke verhalen of eigenschappen erin moeten en welke sfeer je zoekt. Je hoeft geen tekst te schrijven of muzikale termen te kennen: gewone, concrete antwoorden zijn genoeg. Hoe meer herkenbare details je deelt, hoe persoonlijker het resultaat.' },
  { question: 'Wat krijg ik precies?', answer: 'Je kiest uit verschillende korte muzikale opties. Van jouw favoriete optie maken we één compleet persoonlijk nummer. Daarna verzorgen wij de publicatie op Spotify en Apple Music en sturen we je de links om het nummer te beluisteren en te delen.' },
  { question: 'Wat kost een nummer?', answer: offer.hasDiscount.value ? `Normaal ${offer.formattedRegular}, nu ${offer.formattedSale}. De muzikale opties, één compleet nummer en de publicatie op Spotify en Apple Music zijn inbegrepen.` : `${offer.formattedRegular}. De muzikale opties, één compleet nummer en de publicatie op Spotify en Apple Music zijn inbegrepen.` },
  { question: 'Hoe kies ik mijn favoriete optie?', answer: 'Zodra de opties klaarstaan, ontvang je per e-mail een persoonlijke link. Daar kun je alle voorproefjes rustig beluisteren en jouw favoriet selecteren. Met die keuze gaan wij verder; je hoeft niets te downloaden, uploaden of technisch in te stellen.' },
  { question: 'Wat gebeurt er nadat ik mijn favoriet heb gekozen?', answer: 'Wij maken jouw gekozen richting af tot een compleet nummer en zetten de publicatie in gang. Zodra het nummer op Spotify en Apple Music staat, mailen we je de directe links. Vanaf dat moment kun je het nummer steeds opnieuw beluisteren en met anderen delen.' },
  { question: 'Moet ik zelf een Spotify- of artiestenaccount hebben?', answer: 'Nee. Wij verzorgen de publicatie voor je. Je hebt geen artiestenprofiel, distributeur of technische kennis nodig en hoeft zelf niets te uploaden. Wij zorgen dat jouw complete nummer als release op Spotify en Apple Music komt te staan.' },
  { question: 'Kan ik mijn nummer later opnieuw beluisteren?', answer: 'Ja. Jouw nummer wordt gepubliceerd als een echte release en blijft via Spotify en Apple Music beschikbaar om opnieuw te beluisteren. Bewaar de luisterlink of voeg het nummer toe aan je bibliotheek, zodat je het bijzondere moment altijd terug kunt halen.' },
  { question: 'Hoe snel heb ik mijn nummer?', answer: 'Binnen 24–72 uur na betaling ontvang je de muzikale opties. Nadat je jouw favoriet hebt gekozen, duurt het meestal nog 24–72 uur voordat de complete versie op Spotify en Apple Music staat. Heb je een vaste datum? Vraag dan ruim op tijd aan.' },
  { question: 'Hoe persoonlijk wordt het nummer?', answer: 'Dat hangt vooral af van jouw input. Namen, herinneringen, typische uitspraken, plekken en inside jokes maken het verschil tussen een algemeen nummer en een verhaal dat echt herkenbaar voelt.' },
  { question: 'Kan ik zelf het genre, tempo en de stem kiezen?', answer: 'Ja. In de briefing kies je de gewenste sfeer, muzikale richting, snelheid en uitvoering. Twijfel je? Kies dan “Laat ons kiezen”, zodat we kunnen gaan voor wat het beste bij jouw verhaal past.' },
  { question: 'Heb ik muziekkennis nodig?', answer: 'Nee. Jij kent het verhaal; wij vertalen het naar muziek. Je kunt in gewone woorden aangeven of het nummer bijvoorbeeld feestelijk, warm, stoer, ontroerend of juist grappig moet voelen. Wij zorgen voor de muzikale uitwerking.' },
  { question: 'Kan ik meerdere namen laten terugkomen?', answer: 'Ja. Je vult eerst één hoofdnaam in en kunt daarna extra namen, koosnamen, rollen of afzenders toevoegen. Deel alleen de namen die echt belangrijk zijn voor het verhaal.' },
  { question: 'Zijn de muzikale opties al complete nummers?', answer: 'Nee. De opties zijn korte voorproefjes waarmee je de sfeer en uitvoering kunt vergelijken. Je kiest één favoriet en van die uitvoering maken we het complete nummer.' },
  { question: 'Sommige nummers staan al op Spotify — hoe zit dat?', answer: 'Voor populaire momenten zoals geslaagd, rijbewijs en eigen huis hebben we al een nummer met heel veel namen. Staat jouw naam er niet bij, dan maken we op aanvraag een eigen versie.' },
  { question: 'Kan ik het voor iemand anders aanvragen?', answer: 'Zeker. Vul de naam en details van diegene in en houd de verrassing geheim tot het grote moment.' },
  { question: 'Mijn gelegenheid staat er niet tussen. Kan ik toch aanvragen?', answer: 'Ja. Kies op de aanvraagpagina voor “Een ander moment”. Van pensioen, jubileum en afscheid tot bedankje, familieherinnering of een gelegenheid die alleen voor jullie betekenis heeft: vertel ons wat er speelt en wij maken de vragen passend.' },
  { question: 'Hoe ontvang ik de opties en updates?', answer: 'We sturen je persoonlijke keuzelink, statusupdates en uiteindelijke luisterlinks naar het e-mailadres dat je tijdens de aanvraag invult. Gebruik daarom een adres dat je regelmatig controleert en kijk eventueel ook in je map met ongewenste e-mail.' },
  { question: 'Kan ik het nummer delen met familie, vrienden of collega’s?', answer: 'Ja. Zodra het nummer op Spotify en Apple Music staat, kun je de link eenvoudig delen via WhatsApp, e-mail en social media.' },
  { question: 'Is de publicatie op Spotify en Apple Music inbegrepen?', answer: 'Ja. Wij publiceren jouw gekozen, complete nummer op beide platforms. Dat zit bij de prijs inbegrepen; jij hoeft daar niets extra’s voor te regelen of te betalen.' },
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
