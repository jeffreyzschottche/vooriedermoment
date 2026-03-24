<script setup lang="ts">
useSeoMeta({
  title: 'Home',
  ogTitle: 'Voor Ieder Moment',
  description: 'Een uniek nummer voor elk verhaal, van intieme herinnering tot groots jubileum.',
});

const projects = [
  {
    type: 'Huwelijk',
    title: 'Sophie & Mark',
    text: 'Een romantische ballad die het verhaal vertelt van hun eerste ontmoeting in Parijs tot aan de grote dag.',
    image:
      'https://lh3.googleusercontent.com/aida-public/AB6AXuCofNHuuklQnnSvriN8xB2y7KFlrwf2bjHsIs9y-t0fYq_hXxDoFuqS-UEUGWux4MVnvo2CBrk5Sthq9hJ9zkKfPHR1irrnPozVQvkgvO-u0w_56dClO7x428evqStmAxTmk8lf4f_cbTULxl_fXtHNtnh5OgWzimDwTHmx1xnonOdajGj65kWJ1ZdU1WG18-G8UXhFMTnnATuJgAkYIaQKDv_mNqETs-FF2fd9xomdnnvW9aLWg6bRfAPhqOF4JpogT5ULXs-MYus',
  },
  {
    type: 'Jubileum',
    title: 'Gouden Jaren',
    text: 'Een vrolijk, uptempo nummer voor Opa Jan die 50 jaar in het vak zit, vol inside jokes en herinneringen.',
    image:
      'https://lh3.googleusercontent.com/aida-public/AB6AXuDfg0pkcL_xqwyrGaEwWcMTz4VHVLWNtvHBQH4CrP7E_nFAC3oYre-0isPgl6SDDAk6ii34_-UqbErovad9OShLw_UNq0TlHTycnJECELZkSUWAed3zjWZBNJ3AX_zaCSczapa7RdbLNONJ7xW7g3ovLSCWMdn6CCHIwL3OgBcnYzR4QYmeJb1GEBRgTLUdhgYbeo70sKe-xr99upO-jrnbxAW6vVB9mFGN1GlyU7fxZ4MF2rmT9SI20UdM_Haew8s60rqabm4o3rI',
  },
  {
    type: 'Afscheid',
    title: 'Lichtpuntje',
    text: 'Een intiem akoestisch eerbetoon ter nagedachtenis aan een geliefde oma, gefocust op haar warmte en kracht.',
    image: '',
  },
];

const testimonials = [
  'Ik wist niet wat ik moest verwachten, maar het resultaat was adembenemend. Het nummer op onze bruiloft zorgde voor geen enkel droog oog.',
  'Het perfecte cadeau voor mijn vaders 60e verjaardag. De tekst klopte tot in de kleinste details. Een herinnering voor het leven.',
  'Snel geleverd en van professionele studiokwaliteit. De Spotify publicatie maakte het helemaal af om te delen met familie.',
];

const faqPreview = [
  {
    question: 'Hoe werkt het proces precies?',
    answer: 'Na je aanvraag matchen we je verhaal met de juiste componist. Binnen 48 uur ontvang je het unieke nummer digitaal.',
  },
  {
    question: 'Kan ik de tekst zelf aanleveren?',
    answer: 'Zeker. Je kunt volledige teksten aanleveren of alleen steekwoorden en herinneringen. Wij zorgen voor het ritme en de rijm.',
  },
  {
    question: 'Is de muziek echt voor mij?',
    answer: 'Ja, het nummer is uniek voor jouw moment. Bij het basis pakket krijg je de gebruiksrechten voor eigen kring.',
  },
];
</script>

<template>
  <div>
    <section class="relative min-h-[calc(100vh-64px)] overflow-hidden">
      <div class="absolute inset-0">
        <img
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuC5lEiKRIzOvuhwZHgvUXPyLomyLVqE5dxO24SeLDWt4iRCh1_a3Wblr_-0EsPB2KL41eIvf2PdFZ4fHckJPJypppTuStXJzCp4_RP9AlTJzpVWQy9nxfG3TrBtWUgiotC4mj2ZxK7Ibn81nwAlgvu8BWsj1Fdv_3mEhugohprM0hIKcDVAZu_lnqTXN1c5cr_cvgQDBj2hh43rvlRAVclVqChjHpCZ9yblNj3uEsUuuGEdm60owk-Hr_t9ms9uKZ5GfR3aCHdQK4s"
          alt=""
          class="h-full w-full object-cover opacity-35 mix-blend-luminosity"
        />
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(19,19,19,0.54),rgba(19,19,19,0.78),#131313)]" />
      </div>

      <div class="site-container relative flex min-h-[calc(100vh-64px)] items-center justify-center py-16 text-center sm:py-20">
        <div class="max-w-5xl">
          <span class="section-kicker">Personaliseer je herinnering</span>
          <h1 class="section-heading text-5xl leading-[0.92] sm:text-6xl md:text-8xl">
            Voor Ieder Moment
            <span class="block italic text-[var(--color-primary)]">een uniek nummer.</span>
          </h1>
          <p class="mx-auto mt-8 max-w-3xl text-base leading-8 text-[var(--color-on-surface-muted)] md:text-xl">
            Wij vertalen uw meest kostbare verhalen naar professionele, op maat gemaakte muziek.
            Voor een bruiloft, afscheid of jubileum, wij vangen het gevoel.
          </p>
          <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
            <NuxtLink to="/aanvraag" class="stitch-button px-8 py-3.5">Vraag een nummer aan</NuxtLink>
            <a href="#muziek" class="stitch-outline-button px-8 py-3.5">Beluister voorbeelden</a>
          </div>
          <div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs font-semibold uppercase tracking-[0.24em] text-[var(--color-on-surface-muted)]">
            <span>Binnen 48 uur mogelijk</span>
            <span class="hidden h-1.5 w-1.5 rounded-full bg-[var(--color-primary)] sm:block" />
            <span>Persoonlijk gecomponeerd</span>
            <span class="hidden h-1.5 w-1.5 rounded-full bg-[var(--color-primary)] sm:block" />
            <span>Spotify-optie beschikbaar</span>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-[#171717] px-5 py-16 sm:px-8 sm:py-20" id="muziek">
      <div class="mx-auto max-w-6xl">
        <div class="mb-12 text-center">
          <h2 class="section-heading text-4xl md:text-5xl">Recent Voltooide Nummers</h2>
          <p class="section-subtext mx-auto mt-3 max-w-2xl">
            Bekijk een selectie van verhalen die we onlangs hebben omgezet in muziek.
          </p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
          <article v-for="project in projects" :key="project.title" class="rich-card overflow-hidden transition duration-300 hover:-translate-y-1 hover:border-[rgba(242,202,80,0.24)]">
            <div v-if="project.image" class="aspect-square overflow-hidden">
              <img :src="project.image" :alt="project.title" class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]" />
            </div>
            <div v-else class="aspect-square bg-[#3a3a3a]" />
            <div class="p-6">
              <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-[var(--color-primary)]">{{ project.type }}</p>
              <h3 class="mt-3 font-display text-2xl font-bold text-white">{{ project.title }}</h3>
              <p class="mt-3 text-sm leading-6 text-[var(--color-on-surface-muted)]">{{ project.text }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-[#201f1f] px-5 py-16 sm:px-8 sm:py-20">
      <div class="mx-auto max-w-6xl">
        <h2 class="section-heading mb-12 text-center text-4xl md:text-5xl">Wat onze klanten zeggen</h2>
        <div class="grid gap-8 md:grid-cols-3">
          <article v-for="(quote, index) in testimonials" :key="index" class="panel p-8 text-center">
            <p class="mb-5 text-4xl text-[var(--color-primary)]">"</p>
            <p class="font-display text-2xl italic leading-9 text-white">{{ quote }}</p>
            <p class="mt-6 text-xs font-bold uppercase tracking-[0.22em] text-[var(--color-primary)]">
              {{ ['Annelies V.', 'Thomas de B.', 'Sarah L.'][index] }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <section class="px-5 py-16 sm:px-8 sm:py-20">
      <div class="mx-auto max-w-5xl">
        <div class="mb-12 text-center">
          <h2 class="section-heading text-4xl md:text-5xl">Kies jouw pakket</h2>
          <p class="section-subtext mt-3">Transparante prijzen, geen verborgen kosten.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
          <article class="rich-card p-8">
            <h3 class="font-display text-3xl font-bold text-white">Digitaal Basis</h3>
            <p class="mt-4 text-4xl font-bold text-white">€5,99</p>
            <ul class="mt-6 space-y-3 text-sm leading-6 text-[var(--color-on-surface-muted)]">
              <li>Hoogwaardige MP3/WAV file</li>
              <li>Levering binnen 48 uur</li>
              <li>Persoonlijk gecomponeerd</li>
              <li class="opacity-30 line-through">Publicatie op Spotify</li>
            </ul>
            <NuxtLink to="/aanvraag" class="stitch-outline-button mt-8 flex w-full py-3.5">
              Kies Basis
            </NuxtLink>
          </article>

          <article class="rich-card relative border-2 border-[var(--color-primary)] bg-[#2a2a2a] p-8">
            <div class="mb-3 inline-flex rounded-full bg-[var(--color-primary)] px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[var(--color-on-primary)]">
              Meest Gekozen
            </div>
            <h3 class="font-display text-3xl font-bold text-white">Premium Spotify</h3>
            <p class="mt-4 text-4xl font-bold text-white">€9,99</p>
            <ul class="mt-6 space-y-3 text-sm leading-6 text-[var(--color-on-surface-muted)]">
              <li>Alles uit het Basis pakket</li>
              <li>Publicatie op Spotify & Apple Music</li>
              <li>Vermelding op officiële playlists</li>
              <li>Deelbare link voor social media</li>
            </ul>
            <NuxtLink to="/aanvraag" class="stitch-button mt-8 flex w-full py-3.5">Kies Premium</NuxtLink>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-[#1a1a1a] px-5 py-16 sm:px-8 sm:py-20">
      <div class="mx-auto max-w-3xl">
        <h2 class="section-heading mb-8 text-center text-3xl">Veelgestelde Vragen</h2>
        <div class="space-y-4">
          <article v-for="item in faqPreview" :key="item.question" class="rich-card p-6 sm:p-7">
            <h3 class="flex items-center justify-between text-base font-bold text-white">
              {{ item.question }}
              <span class="text-[var(--color-primary)]">⌄</span>
            </h3>
            <p class="mt-3 text-sm leading-7 text-[var(--color-on-surface-muted)]">{{ item.answer }}</p>
          </article>
        </div>
        <div class="mt-8 text-center">
          <NuxtLink to="/faq-contact" class="text-sm font-bold text-[var(--color-primary)]">Bekijk alle vragen →</NuxtLink>
        </div>
      </div>
    </section>

    <section class="px-5 py-20 sm:px-8 sm:py-24">
      <div class="mx-auto max-w-4xl text-center">
        <div class="soft-divider mb-10" />
        <h2 class="section-heading text-5xl leading-tight md:text-7xl">
          Klaar voor jouw eigen
          <span class="italic text-[var(--color-primary)]">soundtrack?</span>
        </h2>
        <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[var(--color-on-surface-muted)]">
          Gun jezelf of een ander een herinnering die nooit vervaagt. Begin vandaag nog aan jouw nummer.
        </p>
        <NuxtLink to="/aanvraag" class="stitch-button mt-10 px-10 py-4">Nu Aanvragen</NuxtLink>
      </div>
    </section>
  </div>
</template>
