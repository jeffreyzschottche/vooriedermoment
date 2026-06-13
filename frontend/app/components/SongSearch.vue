<script setup lang="ts">
// "Staat jouw naam erbij?" — voor de existing-categorieën.
// De echte namenlijst/playlist wordt later gekoppeld. Nu: een zoekveld dat
// altijd doorverwijst naar een eigen aanvraag als de naam (nog) niet gevonden is.
const props = defineProps<{ hook?: string; requestHref: string }>();

const query = ref('');
const searched = ref(false);

function search() {
  searched.value = query.value.trim().length > 0;
}
</script>

<template>
  <div class="rich-card p-6 sm:p-8">
    <h3 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">
      {{ hook ?? 'Staat jouw naam erbij?' }}
    </h3>
    <p class="mt-2 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
      Zoek je naam in het nummer. Zit hij er (nog) niet bij? Dan maken we hem op aanvraag.
    </p>

    <form class="mt-5 flex flex-col gap-3 sm:flex-row" @submit.prevent="search">
      <input
        v-model="query"
        type="text"
        class="field-input flex-1"
        placeholder="Typ je naam…"
        aria-label="Zoek je naam"
      />
      <button type="submit" class="stitch-button whitespace-nowrap">Zoeken</button>
    </form>

    <div
      v-if="searched"
      class="mt-5 rounded-2xl border p-4 text-sm"
      :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }"
    >
      <p :style="{ color: 'var(--color-ink)' }">
        We kunnen de volledige namenlijst hier binnenkort doorzoeken. Wil je zeker weten dat
        <strong>“{{ query }}”</strong> erin zit? Vraag dan direct je eigen versie aan.
      </p>
      <NuxtLink :to="requestHref" class="stitch-outline-button mt-4">
        Vraag jouw versie aan →
      </NuxtLink>
    </div>
  </div>
</template>
