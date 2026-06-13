<script setup lang="ts">
// Toont de prijs. Bij saleOn: €14,99 doorgestreept + €9,99 + label.
withDefaults(defineProps<{ size?: 'sm' | 'md' | 'lg'; inline?: boolean }>(), {
  size: 'md',
  inline: false,
});

const offer = useOffer();
</script>

<template>
  <div :class="inline ? 'inline-flex items-baseline gap-3' : 'flex flex-wrap items-baseline gap-3'">
    <span v-if="offer.saleOn.value" class="price-strike" :class="size === 'lg' ? 'text-xl' : ''">
      {{ offer.formattedRegular }}
    </span>
    <span
      class="font-display font-semibold leading-none"
      :class="{
        'text-2xl': size === 'sm',
        'text-4xl': size === 'md',
        'text-6xl': size === 'lg',
      }"
      :style="{ color: 'var(--accent-strong)' }"
    >
      {{ offer.formattedCurrent.value }}
    </span>
    <span
      v-if="offer.saleOn.value"
      class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]"
      :style="{ background: 'var(--accent)', color: 'var(--accent-ink)' }"
    >
      Aanbieding
    </span>
  </div>
</template>
