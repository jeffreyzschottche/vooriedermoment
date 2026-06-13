<script setup lang="ts">
useSeoMeta({
  title: 'Afronden',
  description: 'Controleer je aanvraag en rond de bestelling af.',
});

const offer = useOffer();
const { current, lastPayload, checkout } = useSongRequest();

const processing = ref(false);

const hasRequest = computed(() => Boolean(lastPayload.value));

async function pay() {
  processing.value = true;
  await checkout();
  processing.value = false;
  await navigateTo('/bedankt');
}
</script>

<template>
  <div class="site-container py-16 sm:py-20">
    <div class="mb-8 flex items-center justify-between gap-4">
      <h1 class="section-heading text-3xl md:text-4xl">Afronden</h1>
      <NuxtLink to="/aanvraag" class="text-sm font-semibold" :style="{ color: 'var(--color-ink-soft)' }">← Terug</NuxtLink>
    </div>

    <!-- Geen lopende aanvraag -->
    <div v-if="!hasRequest" class="rich-card p-8 text-center">
      <p class="text-lg" :style="{ color: 'var(--color-ink)' }">Er is nog geen aanvraag om af te ronden.</p>
      <NuxtLink to="/aanvraag" class="stitch-button mt-6">Start een aanvraag</NuxtLink>
    </div>

    <div v-else class="grid gap-8 lg:grid-cols-[1fr_0.8fr]">
      <!-- Samenvatting -->
      <div class="space-y-6">
        <section class="rich-card p-6 sm:p-8">
          <h2 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">Jouw aanvraag</h2>
          <p class="mt-1 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
            Moment: <strong>{{ lastPayload?.categoryTitle }}</strong>
          </p>
          <dl class="mt-5 grid gap-x-6 gap-y-3 sm:grid-cols-2">
            <div v-for="(val, key) in lastPayload?.intake" :key="key">
              <dt class="text-xs uppercase tracking-[0.12em]" :style="{ color: 'var(--color-ink-faint)' }">{{ key }}</dt>
              <dd class="text-sm" :style="{ color: 'var(--color-ink)' }">{{ val || '—' }}</dd>
            </div>
          </dl>
        </section>

        <section v-if="current?.lyrics_preview" class="rich-card p-6 sm:p-8">
          <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">Concept-tekst (voorproefje)</h2>
          <pre class="mt-4 whitespace-pre-wrap font-sans text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">{{ current.lyrics_preview }}</pre>
        </section>

        <section class="soft-card p-6 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
          <p class="font-semibold" :style="{ color: 'var(--color-ink)' }">Wat gebeurt er na betaling?</p>
          <p class="mt-2">Je ontvangt een bevestiging. Daarna gaan wij je nummer afmaken en leveren we het af via e-mail. Je krijgt één vaste versie die je overal kunt delen.</p>
        </section>
      </div>

      <!-- Betaalkaart -->
      <aside>
        <div class="rich-card p-7 lg:sticky lg:top-24">
          <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">Overzicht</h2>
          <div class="mt-5 space-y-3 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
            <div class="flex justify-between"><span>Persoonlijk nummer</span><span>{{ offer.formattedCurrent.value }}</span></div>
            <div v-if="offer.saleOn.value" class="flex justify-between" :style="{ color: 'var(--accent-strong)' }">
              <span>Aanbiedingskorting</span><span>−{{ offer.formattedRegular.replace('€ ', '€ ') }} → {{ offer.formattedSale }}</span>
            </div>
          </div>
          <div class="mt-5 border-t pt-5" :style="{ borderColor: 'var(--color-line)' }">
            <div class="flex items-end justify-between">
              <span class="text-xs uppercase tracking-[0.14em]" :style="{ color: 'var(--color-ink-faint)' }">Totaal (incl. btw)</span>
              <OfferBadge size="sm" inline />
            </div>
          </div>

          <button class="stitch-button mt-6 w-full py-4 text-base" :disabled="processing" @click="pay">
            {{ processing ? 'Bezig…' : 'Betalen (test) →' }}
          </button>
          <p class="mt-3 rounded-xl px-3 py-2 text-center text-[11px]" :style="{ background: 'var(--color-surface-soft)', color: 'var(--color-ink-faint)' }">
            Demo: betaling is nog gestubd. Er wordt nu niets afgeschreven en het nummer wordt nog niet automatisch geproduceerd.
          </p>
        </div>
      </aside>
    </div>
  </div>
</template>
