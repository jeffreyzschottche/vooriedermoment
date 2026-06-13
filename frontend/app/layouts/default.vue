<script setup lang="ts">
// Organization JSON-LD globaal op elke pagina.
useJsonLd(organizationSchema);
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <AppNav />
    <main class="flex-1">
      <slot />
    </main>
    <SiteFooter />
  </div>
</template>
