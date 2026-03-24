<script setup lang="ts">
useSeoMeta({
  title: 'Over Ons',
  description: 'Lees hoe Voor Ieder Moment persoonlijke verhalen vertaalt naar blijvende muziek.',
});

const embeds = [
  { label: 'Herinnering', title: 'Voor Altijd Bij Ons', src: 'https://open.spotify.com/embed/track/2Z3yRSGcnu0OSCjR7uxtZ2?utm_source=generator&theme=0' },
  { label: 'Viering', title: 'De Dag Van De Liefde', src: 'https://open.spotify.com/embed/track/6UelLqGlWMvD4bdS87Vg3C?utm_source=generator&theme=0' },
  { label: 'Geboorte', title: 'Welkom Kleine Wonder', src: 'https://open.spotify.com/embed/track/30Y89q1r7z0Xq6A22S6k5J?utm_source=generator&theme=0' },
];
</script>

<template>
  <div>
    <section class="relative min-h-[560px] overflow-hidden px-5 sm:px-8 md:px-24">
      <div class="absolute inset-0">
        <div class="absolute inset-0 z-10 bg-[linear-gradient(90deg,#131313,rgba(19,19,19,0.82),transparent)]" />
        <img
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0gHTEDUmhNvIpYXBHFxqiFhP4cCOm2NqBhVWpBr_LGsxfxZwmZolmEIRudsnhnASp5GjnBmaZxELl2-0hhTqK20BkrgNdnJ1bytNJ9ENCdhub3DqzwfWgHzFHd62s5mnV17D5dBI2dDRpKteCnSPISpmq-ondBmZ5e54O4u0o7ksnpi1TI74cgRzAl3HdRUhwcJNCZSHjyTzk6yYCYDUpL605cXSWng87u4yeFVDRVnWejvKj7zDeBuwNX28qkypJIV1Pydjji74"
          alt=""
          class="h-full w-full object-cover opacity-35 grayscale"
        />
      </div>
      <div class="relative z-20 flex min-h-[560px] items-center">
        <div class="max-w-3xl">
          <span class="section-kicker">Onze missie</span>
          <h1 class="section-heading text-5xl leading-tight md:text-7xl">
            Muziek die uw
            <span class="italic text-[var(--color-primary)]">persoonlijke</span>
            verhaal vertelt.
          </h1>
          <p class="mt-8 max-w-2xl text-lg leading-8 text-[var(--color-on-surface-muted)] md:text-xl md:leading-9">
            Wij geloven dat elk bijzonder moment, elke herinnering en elke emotie een eigen melodie verdient.
            Wij vertalen uw woorden naar een uniek muzikaal eerbetoon.
          </p>
        </div>
      </div>
    </section>

    <section class="bg-[#0e0e0e] px-5 py-20 sm:px-8 md:px-24">
      <div class="mx-auto max-w-7xl">
        <div class="mb-12">
          <h2 class="section-heading text-4xl">Hoe Wij Werken</h2>
          <p class="section-subtext mt-3 max-w-xl">
            Van de eerste herinnering tot de uiteindelijke release op Spotify. Een proces van co-creatie en passie.
          </p>
        </div>

        <div class="grid gap-6 md:grid-cols-12 md:grid-rows-[auto_auto]">
          <article class="rich-card p-8 md:col-span-8">
            <p class="font-display text-5xl text-[var(--color-primary)]">01</p>
            <h3 class="mt-5 font-display text-2xl text-white">De Intake & Het Verhaal</h3>
            <p class="mt-4 max-w-xl text-sm leading-7 text-[var(--color-on-surface-muted)]">
              Alles begint bij u. In een persoonlijk gesprek delen we de kern van het verhaal.
              Wat is de boodschap? Welke sfeer moet het lied ademen? Wij luisteren naar de details die uw verhaal uniek maken.
            </p>
          </article>

          <article class="rich-card relative overflow-hidden p-8 text-center md:col-span-4">
            <div class="absolute inset-0 opacity-10">
              <img
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBdu5UNCc3e_0ibWE5_bxuow7UAiA5V7lgSD9T_ow6oXH7Welw759Flzqe5-RpuDn2et50bBl9gqFgoN97n2A7ZY9jk3-ed0na4pF9ItxU2CBm24DzC8bZYX9hZ6UU1SfMC5rQDKRu5J-YVlKl_ZFtB-rLcRuDKPG8RwC8b6KTwEWK2nBSXA9WMhLrp6AcOLLsB4Fi2ZNTUt8UV5U6WrW0xoL_PJvr8DrsesmHQBpJXxBhd9UsUw84xj78lgO1Y61w8ISl3SHwDOx0"
                alt=""
                class="h-full w-full object-cover"
              />
            </div>
            <div class="relative">
              <p class="font-display text-5xl text-[var(--color-primary)]">02</p>
              <h3 class="mt-5 font-display text-xl text-white">Compositie</h3>
              <p class="mt-4 text-sm leading-6 text-[var(--color-on-surface-muted)]">
                Onze componisten schrijven de muziek die naadloos aansluit bij de emotie van uw tekst.
              </p>
            </div>
          </article>

          <article class="rich-card border border-[rgba(242,202,80,0.2)] bg-[rgba(242,202,80,0.08)] p-8 md:col-span-4">
            <p class="font-display text-5xl text-[var(--color-primary)]">03</p>
            <h3 class="mt-5 font-display text-xl text-white">Studio Opname</h3>
            <p class="mt-4 text-sm leading-6 text-[var(--color-on-surface-muted)]">
              Professionele muzikanten en vocalisten brengen het stuk tot leven in een high-end studio omgeving.
            </p>
          </article>

          <article class="rich-card flex flex-col gap-6 p-8 md:col-span-8 md:flex-row md:items-center">
            <div class="flex-1">
              <p class="font-display text-5xl text-[var(--color-primary)]">04</p>
              <h3 class="mt-5 font-display text-2xl text-white">De Release</h3>
              <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">
                Het lied is klaar. U ontvangt een luxe digitale verpakking en indien gewenst publiceren wij het nummer op Spotify,
                zodat u het voor altijd kunt delen met de wereld.
              </p>
            </div>
            <div class="flex aspect-square w-full max-w-[220px] flex-col items-center justify-center rounded-[1.25rem] border border-[rgba(77,70,53,0.16)] bg-[#111]">
              <div class="text-5xl text-[var(--color-primary)]">◉</div>
              <p class="mt-4 text-xs uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Spotify Certified</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="px-5 py-24 text-center sm:px-8">
      <div class="mx-auto max-w-4xl">
        <h2 class="font-display text-4xl italic text-white md:text-6xl">"Muziek is de kortste weg naar het hart."</h2>
        <div class="mx-auto mt-10 h-px w-24 bg-[var(--color-primary)]" />
        <p class="mt-10 text-lg leading-8 text-[var(--color-on-surface-muted)] md:text-xl md:leading-9">
          Voor Ieder Moment is ontstaan uit een diepgewortelde passie voor de kracht van muziek.
          Wij geloven dat standaardliedjes vaak net niet de kern raken van wat u écht wilt zeggen.
          Daarom hebben wij een platform gecreëerd waar vakmanschap en persoonlijke empathie samenkomen om iets blijvends te creëren.
        </p>
      </div>
    </section>

    <section class="bg-[#1c1b1b] px-5 py-20 sm:px-8 md:px-24">
      <div class="mx-auto max-w-7xl">
        <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <span class="section-kicker">Portfolio</span>
            <h2 class="section-heading text-4xl">Beluister Onze Momenten</h2>
          </div>
          <a href="https://open.spotify.com" target="_blank" rel="noopener noreferrer" class="text-sm text-[var(--color-primary)]">
            Bekijk alles op Spotify →
          </a>
        </div>

        <div class="grid gap-8 md:grid-cols-3">
          <article v-for="embed in embeds" :key="embed.title" class="rich-card overflow-hidden bg-[#0e0e0e] p-1">
            <iframe
              :src="embed.src"
              width="100%"
              height="352"
              frameborder="0"
              allowfullscreen
              allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
              loading="lazy"
              style="border-radius: 12px"
            />
            <div class="p-6">
              <p class="text-xs uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">{{ embed.label }}</p>
              <h3 class="mt-2 font-display text-lg text-white">{{ embed.title }}</h3>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-[#0e0e0e] px-5 py-24 text-center sm:px-8">
      <div class="mx-auto max-w-2xl">
        <h2 class="section-heading text-4xl md:text-5xl">Klaar om uw verhaal te laten klinken?</h2>
        <div class="mt-10 flex flex-col items-center justify-center gap-5 md:flex-row">
          <NuxtLink to="/aanvraag" class="stitch-button px-10 py-4">Start uw aanvraag</NuxtLink>
          <NuxtLink to="/faq-contact" class="text-sm font-semibold text-[var(--color-on-surface-muted)]">Plan een belafspraak →</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>
