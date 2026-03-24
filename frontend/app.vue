<script setup lang="ts">
useHead({
  htmlAttrs: {
    lang: 'nl',
    class: 'scroll-smooth',
  },
  bodyAttrs: {
    class: 'bg-[var(--color-surface)] text-[var(--color-on-surface)]',
  },
});
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>
