<script setup lang="ts">
useSeoMeta({
  title: 'Checkout',
  description: 'Controleer de aanvraag en rond de bestelling af.',
});

const withStreaming = ref(true);
const total = computed(() => (withStreaming.value ? '€ 9,99' : '€ 5,99'));
</script>

<template>
  <div class="px-5 py-14 sm:px-8 sm:py-16">
    <div class="mx-auto max-w-7xl">
      <div class="mb-10 flex items-center justify-between gap-4">
        <h1 class="font-display text-3xl font-bold text-[var(--color-primary)] md:text-5xl">Voor Ieder Moment</h1>
        <NuxtLink to="/aanvraag" class="text-sm text-[var(--color-on-surface-muted)]">← Terug naar ontwerp</NuxtLink>
      </div>

      <div class="grid gap-8 lg:grid-cols-[1fr_0.78fr]">
        <div class="space-y-8">
          <section>
            <h2 class="section-heading text-5xl">Rond uw aanvraag af</h2>
            <p class="mt-4 max-w-2xl text-lg leading-8 text-[var(--color-on-surface-muted)]">
              Bijna klaar. Controleer uw bestelling en kies een betaalmethode om uw unieke verhaal tot leven te wekken.
            </p>
          </section>

          <section class="rich-card p-6 sm:p-8">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-display text-3xl text-[var(--color-primary)]">Gepersonaliseerd nummer</h3>
                <p class="mt-2 text-sm text-[var(--color-on-surface-muted)]">Gecomponeerd op basis van uw verhaal</p>
              </div>
              <p class="text-xl font-semibold text-white">€ 5,99</p>
            </div>

            <label class="mt-8 flex cursor-pointer items-start gap-4 border-t border-[rgba(77,70,53,0.18)] pt-6">
              <input v-model="withStreaming" type="checkbox" class="mt-1 h-4 w-4 rounded-sm border-none text-[var(--color-primary-deep)]" />
              <span class="flex-1">
                <span class="block text-lg text-white">Spotify & Apple Music publicatie</span>
                <span class="mt-1 block text-sm text-[var(--color-on-surface-muted)]">Wij regelen de distributie naar alle streamingplatforms.</span>
              </span>
              <span class="text-lg text-[var(--color-on-surface-muted)]">+ € 4,00</span>
            </label>
          </section>

          <section>
            <div class="mb-4 text-xs uppercase tracking-[0.28em] text-[var(--color-primary)]">Betaalmethode</div>
            <div class="rounded-[1.5rem] border-2 border-[var(--color-primary)] bg-[#2a2a2a] p-5 shadow-[0_18px_40px_rgba(0,0,0,0.18)]">
              <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="flex h-10 w-12 items-center justify-center rounded-sm bg-[#ec008c] text-xs font-extrabold text-white">iDEAL</div>
                  <span class="font-semibold text-white">iDEAL (Alle Nederlandse banken)</span>
                </div>
                <div class="h-4 w-4 rounded-full border-2 border-[var(--color-primary)]" />
              </div>
            </div>
          </section>

          <section class="rich-card bg-[#111] p-8">
            <h3 class="section-heading text-4xl">Wat gebeurt er na betaling?</h3>
            <div class="mt-8 grid gap-8 sm:grid-cols-2">
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--color-primary)]">Bevestiging</p>
                <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">U ontvangt direct een mail met de samenvatting van uw bestelling en het proces.</p>
              </div>
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--color-primary)]">Compositie</p>
                <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">Onze artiesten gaan aan de slag. Binnen 72 uur is uw persoonlijke nummer klaar.</p>
              </div>
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--color-primary)]">Levering</p>
                <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">U ontvangt de master-bestanden (MP3 & WAV) van studiokwaliteit via een beveiligde link.</p>
              </div>
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--color-primary)]">Publicatie</p>
                <p class="mt-4 text-sm leading-7 text-[var(--color-on-surface-muted)]">Indien gekozen, is uw nummer binnen enkele dagen te vinden op Spotify & Apple Music.</p>
              </div>
            </div>
          </section>
        </div>

        <aside class="space-y-8">
          <div class="rich-card bg-[#151515] p-8 lg:sticky lg:top-28">
            <h3 class="section-heading text-4xl">Overzicht</h3>
            <div class="mt-8 space-y-4 text-lg text-[var(--color-on-surface-muted)]">
              <div class="flex justify-between">
                <span>Basisprijs Nummer</span>
                <span>€ 5,99</span>
              </div>
              <div class="flex justify-between">
                <span>Streaming Publicatie</span>
                <span>{{ withStreaming ? '€ 4,00' : 'Niet gekozen' }}</span>
              </div>
              <div class="flex justify-between">
                <span>Servicekosten</span>
                <span>Inbegrepen</span>
              </div>
            </div>
            <div class="mt-8 border-t border-[rgba(77,70,53,0.12)] pt-8 text-right">
              <p class="text-xs uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Inclusief BTW</p>
              <p class="mt-2 font-display text-5xl font-bold text-[var(--color-primary)]">{{ total }}</p>
            </div>
            <button class="stitch-button mt-8 flex w-full py-4 text-base">Betalen via iDEAL</button>
            <p class="mt-4 text-center text-[10px] leading-5 text-[var(--color-on-surface-muted)]">
              Door op 'Betalen' te klikken gaat u akkoord met onze Algemene Voorwaarden en Privacybeleid. Uw gegevens zijn 100% veilig.
            </p>
            <div class="mt-10 flex items-center justify-center gap-10 text-center text-[10px] uppercase tracking-[0.12em] text-[var(--color-on-surface-muted)]">
              <div>Safe Checkout</div>
              <div>HQ Audio</div>
              <div>72h Delivery</div>
            </div>
          </div>

          <div class="relative overflow-hidden rounded-[1.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.24)]">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBvRoawM8yXpyb7ApA57Kja8k81zhCDeRipLO5pd_qYFzfCi0eKAzDrNyUCTIj3P63IeoNIyOt8h6uDp8pUg_Vkrfnl57irv5a_i_vMZSoY4dwfBnZtkiTIpF4L_8FuWziO-SnOB8My3rya2TxHxZEMVWyvlgk4m55k5D7otImF390hWVKdzGOznJ4eQyjroAj3p7hkB4yWk8xSowsTPNfP1J_c2LjTfmd7fkV1a6BDqqMyjlH7UofHy3xdt8v0DOfbugHt509NXMQ"
              alt=""
              class="h-64 w-full object-cover grayscale"
            />
            <div class="absolute inset-0 bg-[linear-gradient(180deg,transparent,rgba(19,19,19,0.7),#131313)]" />
            <p class="absolute bottom-5 left-5 font-display text-2xl italic text-white">"Een herinnering die voor altijd klinkt."</p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>
