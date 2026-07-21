<script setup lang="ts">
useSeoMeta({
  title: 'Afronden',
  description: 'Controleer je aanvraag en rond de bestelling af.',
});

import { themeVars } from '~/data/categories';

const offer = useOffer();
const { current, lastPayload, checkout, theme } = useSongRequest();

const processing = ref(false);

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
  await checkout();
  processing.value = false;
  await navigateTo('/bedankt');
}

function fallbackLine(value: unknown, fallback: string) {
  const text = Array.isArray(value)
    ? value.filter(Boolean).join(', ')
    : String(value ?? '').trim();
  return text || fallback;
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
  if (preview) {
    const lines = preview.split('\n').filter((line) => line.trim());
    return [
      ...lines,
      '[Pre-chorus]',
      'We bouwen de spanning op met jullie eigen woorden',
      'De melodie tilt het moment naar voren',
      '[Refrein]',
      'Een herkenbare hook met naam, verhaal en gevoel',
      'Groot genoeg om mee te zingen, persoonlijk genoeg voor kippenvel',
      '[Couplet 2]',
      'Hier komen de extra herinneringen en inside jokes terug',
      'Met details die alleen jullie groep meteen herkent',
      '[Bridge]',
      'Een kort rustpunt voordat het laatste refrein groter terugkomt',
      '[Laatste refrein]',
      'De belangrijkste zin komt nog een keer helder naar voren',
      'Het nummer eindigt met warmte, energie en herkenning',
    ];
  }

  const intake = lastPayload.value?.intake ?? {};
  const name = fallbackLine(intake.recipientName, 'de hoofdnaam');
  const from = fallbackLine(intake.fromName, 'de mensen eromheen');
  const extraNames = fallbackLine(intake.additionalRecipientNames, 'extra namen, rollen en relaties');
  const story = fallbackLine(intake.anecdotesItems ?? intake.anecdotes, 'jullie verhaal, anekdotes en inside jokes');
  const must = fallbackLine(intake.mustMentionItems ?? intake.mustMention, 'wat absoluut in het nummer moet');
  const tone = fallbackLine(intake.tone, 'de gekozen sfeer');
  const genre = fallbackLine(intake.musicStyle, 'het gekozen genre');
  const tempo = fallbackLine(intake.tempo, 'het gekozen tempo');

  return [
    '[Intro]',
    `Voor ${name}, van ${from}`,
    `Een ${tone.toLowerCase()} begin in ${genre.toLowerCase()}`,
    '[Couplet 1]',
    `We starten bij ${name} en het moment dat iedereen herkent`,
    `Met woorden van ${from}, recht uit het hart en niet generiek`,
    story,
    '[Pre-chorus]',
    `Het tempo voelt ${tempo.toLowerCase()}, met ruimte voor elk detail`,
    'De melodie maakt het persoonlijk zonder te zwaar te worden',
    '[Refrein]',
    must,
    `Een hook waarin ${name} blijft hangen`,
    '[Couplet 2]',
    `Hier komen ${extraNames} terug`,
    'Een tweede laag met herinneringen die de tekst eigen maken',
    '[Bridge]',
    'Even kleiner, dichterbij, alsof iemand het persoonlijk vertelt',
    '[Laatste refrein]',
    `Nog een keer groot voor ${name}, klaar om te delen`,
    'Met een einde dat voelt als jullie moment',
  ];
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
              <span class="chip shrink-0">{{ lyricsLines.length }} regels</span>
            </div>

            <div
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
              class="mt-4 rounded-xl px-4 py-3 text-center text-xs leading-relaxed"
              :style="{ background: 'var(--color-surface-soft)', color: 'var(--color-ink-faint)' }"
            >
              Demo: betaling is gestubd. Na betaling ontvang je binnen 24–72 uur 4 samples per mail.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>
