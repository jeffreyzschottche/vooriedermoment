<script setup lang="ts">
useSeoMeta({
  title: 'Afronden',
  description: 'Controleer je aanvraag en rond de bestelling af.',
});

import { themeVars } from '~/data/categories';

const offer = useOffer();
const { current, lastPayload, checkout, theme } = useSongRequest();

const processing = ref(false);
const paymentError = ref('');

// Houd dezelfde categoriekleur vast als in het formulier.
const themeStyle = computed(() => themeVars(theme.value));

const hasRequest = computed(() => Boolean(lastPayload.value));
const fieldLabels: Record<string, string> = {
  recipientName: 'Voor wie',
  fromName: 'Afzender',
  additionalRecipientNames: 'Extra namen in nummer',
  additionalSenderNames: 'Extra afzenders',
  tone: 'Sfeer',
  vocals: 'Stem',
  musicStyle: 'Genre',
  tempo: 'Snelheid / tempo',
  anecdotes: 'Verhaal',
  anecdotesItems: 'Situaties',
  mustMention: 'Moet erin',
  mustMentionItems: 'Losse must-haves',
  avoid: 'Vermijden',
  companyName: 'Bedrijfsnaam',
  clubName: 'Club/team',
};

async function pay() {
  processing.value = true;
  paymentError.value = '';

  try {
    const result = await checkout();

    if (result?.checkout_url) {
      await navigateTo(result.checkout_url, { external: true });
      return;
    }

    await navigateTo('/bedankt');
  } catch {
    paymentError.value = 'Betalen kon niet worden gestart. Probeer het opnieuw of neem contact met ons op.';
  } finally {
    processing.value = false;
  }
}

function shouldShowIntakeField(key: string) {
  return !['anecdotes', 'mustMention'].includes(key)
    || !Array.isArray(lastPayload.value?.intake?.[`${key}Items`]);
}

function formatIntakeValue(value: unknown) {
  if (Array.isArray(value)) {
    const lines = value.filter(Boolean).map((item, index) => `${index + 1}. ${item}`);
    return lines.length ? lines.join('\n') : '—';
  }

  return String(value || '—');
}

const lyricsLines = computed(() => {
  const preview = current.value?.lyrics_preview?.trim();
  return preview
    ? preview.split('\n').filter((line) => line.trim())
    : [];
});

function isVisibleLyricLine(index: number) {
  const line = lyricsLines.value[index]?.trim() ?? '';
  if (/^\[[^\]]+\]$/.test(line)) {
    return false;
  }

  return ((index * 7) + lyricsLines.value.length) % 4 === 1;
}
</script>

<template>
  <div class="py-16 sm:py-24" :style="themeStyle">
    <div class="site-container">
      <!-- Header -->
      <div v-reveal class="mb-10 flex items-center justify-between gap-4">
        <div>
          <span class="section-kicker">Bijna klaar</span>
          <h1 class="section-heading text-3xl sm:text-4xl">Afronden</h1>
        </div>
        <NuxtLink
          to="/aanvraag"
          class="ghost-button"
        >
          ← Terug
        </NuxtLink>
      </div>

      <!-- Geen lopende aanvraag -->
      <div v-if="!hasRequest" v-reveal class="rich-card p-10 text-center">
        <div class="bento-card-icon mx-auto mb-6 text-3xl">📝</div>
        <p class="text-lg font-medium" :style="{ color: 'var(--color-ink)' }">
          Er is nog geen aanvraag om af te ronden.
        </p>
        <NuxtLink to="/aanvraag" class="stitch-button mt-8">
          Start een aanvraag
        </NuxtLink>
      </div>

      <div v-else class="grid gap-10 lg:grid-cols-[1fr_0.75fr]">
        <!-- Samenvatting -->
        <div class="space-y-8">
          <section v-reveal class="rich-card p-7 sm:p-8">
            <h2 class="font-display text-xl font-semibold sm:text-2xl" :style="{ color: 'var(--color-ink)' }">
              Jouw aanvraag
            </h2>
            <p class="mt-2 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
              Moment: <strong :style="{ color: 'var(--accent-strong)' }">{{ lastPayload?.categoryTitle }}</strong>
            </p>
            <dl class="mt-6 grid gap-x-8 gap-y-4 sm:grid-cols-2">
              <div v-for="(val, key) in lastPayload?.intake" v-show="shouldShowIntakeField(String(key))" :key="key">
                <dt class="text-xs font-bold uppercase tracking-[0.14em]" :style="{ color: 'var(--color-ink-faint)' }">
                  {{ fieldLabels[String(key)] ?? key }}
                </dt>
                <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed" :style="{ color: 'var(--color-ink)' }">
                  {{ formatIntakeValue(val) }}
                </dd>
              </div>
            </dl>
          </section>

          <section v-reveal class="rich-card overflow-hidden p-7 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
              <div>
                <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">
                  Voorproefje van jouw songtekst
                </h2>
                <p class="mt-2 text-sm leading-relaxed" :style="{ color: 'var(--color-ink-soft)' }">
                  Bekijk de opbouw en enkele persoonlijke regels. De rest gebruiken we om jouw vier samples te maken.
                </p>
              </div>
              <span v-if="lyricsLines.length" class="chip shrink-0">{{ lyricsLines.length }} regels</span>
            </div>

            <div
              v-if="lyricsLines.length"
              class="mt-5 max-h-[520px] overflow-hidden rounded-xl border p-5 font-sans text-sm leading-7"
              :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)', color: 'var(--color-ink-soft)' }"
            >
              <p
                v-for="(line, index) in lyricsLines"
                :key="`${line}-${index}`"
                class="min-h-7 whitespace-pre-wrap transition"
                :class="isVisibleLyricLine(index) ? 'font-semibold' : 'select-none blur-[5px]'"
                :style="{ color: isVisibleLyricLine(index) ? 'var(--color-ink)' : 'var(--color-ink-faint)' }"
              >
                {{ line }}
              </p>
            </div>
            <p v-else class="mt-5 rounded-xl border p-5 text-sm" :style="{ borderColor: 'var(--color-line)', color: 'var(--color-ink-soft)' }">
              De echte voorproef kon nog niet worden geladen. Probeer de aanvraag opnieuw voordat je betaalt.
            </p>
          </section>

          <section v-reveal class="soft-card p-7">
            <p class="font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">
              Wat gebeurt er na betaling?
            </p>
            <div v-reveal-stagger data-stagger="0.1" class="mt-6 grid gap-4 sm:grid-cols-2">
              <div class="rich-card p-5">
                <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">
                  Stap 1
                </p>
                <p class="mt-3 font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">
                  4 samples in je inbox
                </p>
                <p class="mt-2 text-sm leading-relaxed" :style="{ color: 'var(--color-ink-soft)' }">
                  Binnen 24–72 uur ontvang je vier unieke samples van 15 seconden. Jij kiest de favoriet.
                </p>
              </div>
              <div class="rich-card p-5">
                <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">
                  Stap 2
                </p>
                <p class="mt-3 font-display text-lg font-semibold" :style="{ color: 'var(--color-ink)' }">
                  Jouw favoriet wordt de complete versie
                </p>
                <p class="mt-2 text-sm leading-relaxed" :style="{ color: 'var(--color-ink-soft)' }">
                  Na jouw keuze maken wij het nummer af. Binnen 24–72 uur staat het op Spotify en Apple Music.
                </p>
              </div>
            </div>
          </section>
        </div>

        <!-- Betaalkaart -->
        <aside v-reveal data-reveal-delay="0.15">
          <div class="rich-card p-8 lg:sticky lg:top-24">
            <h2 class="font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">
              Overzicht
            </h2>

            <div class="mt-6 space-y-4 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
              <div class="flex justify-between">
                <span>Persoonlijk nummer</span>
                <span class="font-semibold" :style="{ color: 'var(--color-ink)' }">{{ offer.formattedCurrent.value }}</span>
              </div>
              <div class="flex justify-between">
                <span>4 samples van 15 seconden</span>
                <span class="text-xs font-medium" :style="{ color: 'var(--accent-strong)' }">Inclusief</span>
              </div>
              <div class="flex justify-between">
                <span>Release op Spotify &amp; Apple Music</span>
                <span class="text-xs font-medium" :style="{ color: 'var(--accent-strong)' }">Inclusief</span>
              </div>
              <div
                v-if="offer.hasDiscount.value"
                class="flex justify-between rounded-lg p-3"
                :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }"
              >
                <span class="font-semibold">Aanbiedingskorting</span>
                <span class="font-bold">-{{ (offer.regularPrice - offer.salePrice).toFixed(2).replace('.', ',') }} korting</span>
              </div>
            </div>

            <div class="mt-6 border-t pt-6" :style="{ borderColor: 'var(--color-line)' }">
              <div class="flex items-end justify-between">
                <span class="text-xs font-bold uppercase tracking-[0.14em]" :style="{ color: 'var(--color-ink-faint)' }">
                  Totaal (incl. btw)
                </span>
                <OfferBadge size="sm" inline />
              </div>
            </div>

            <button
              class="stitch-button mt-8 w-full py-5 text-base"
              :disabled="processing"
              @click="pay"
            >
              <span v-if="processing" class="flex items-center justify-center gap-3">
                <span class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white" />
                Bezig met verwerken...
              </span>
              <span v-else>Bestellen en betalen →</span>
            </button>

            <p
              v-if="paymentError"
              class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-center text-sm text-red-700"
            >
              {{ paymentError }}
            </p>

            <p
              class="mt-4 rounded-xl px-4 py-3 text-center text-xs leading-relaxed"
              :style="{ background: 'var(--color-surface-soft)', color: 'var(--color-ink-faint)' }"
            >
              Je betaalt veilig via Stripe. Na betaling ontvang je binnen 24–72 uur 4 samples per mail.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>
