<script setup lang="ts">
// "Staat jouw naam erbij?" — voor de existing-categorieën.
// De echte namenlijst wordt later gekoppeld. Tot die tijd verwijst deze kaart
// eerlijk en rechtstreeks naar een persoonlijke aanvraag.
defineProps<{ hook?: string; requestHref: string }>();

</script>

<template>
  <div class="rich-card p-6 sm:p-8">
    <h3 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">
      {{ hook ?? 'Staat jouw naam erbij?' }}
    </h3>
    <p class="mt-2 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
      De volledige namenzoeker komt binnenkort online. Wil je nu zeker weten dat jouw naam
      en verhaal in het nummer zitten? Vraag dan meteen een persoonlijke versie aan.
    </p>
    <NuxtLink :to="requestHref" class="stitch-button mt-6">
      Maak mijn eigen versie →
    </NuxtLink>
  </div>
</template>
