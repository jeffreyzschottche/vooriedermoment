<script setup lang="ts">
// Organization + WebSite JSON-LD globaal op elke pagina.
useJsonLd([organizationSchema, websiteSchema]);

// Canonical en og:url voor iedere pagina (zonder query of trailing slash).
const route = useRoute();
const canonicalUrl = computed(() => SITE_URL + (route.path === '/' ? '/' : route.path.replace(/\/+$/, '')));
useHead({ link: [{ rel: 'canonical', href: canonicalUrl, key: 'canonical' }] });
useSeoMeta({ ogUrl: canonicalUrl });
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
