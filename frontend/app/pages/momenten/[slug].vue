<script setup lang="ts">
import { getCategory } from '~/data/categories';

const route = useRoute();
const slug = computed(() => String(route.params.slug));
const category = computed(() => getCategory(slug.value));

// b2b heeft een eigen route; onbekende slug => 404.
if (!category.value || category.value.variant === 'b2b') {
  throw createError({ statusCode: 404, statusMessage: 'Moment niet gevonden', fatal: true });
}

const offer = useOffer();
const c = category.value!;

useSeoMeta({
  title: c.seoTitle,
  ogTitle: c.seoTitle,
  description: c.seoDescription,
  ogDescription: c.seoDescription,
});

useJsonLd([
  productSchema({ name: `${c.title} — persoonlijk nummer`, description: c.seoDescription, price: offer.currentPrice.value, url: `/momenten/${c.slug}` }),
  faqSchema(c.faq),
  breadcrumbSchema([
    { name: 'Home', path: '/' },
    { name: 'Momenten', path: '/momenten' },
    { name: c.title, path: `/momenten/${c.slug}` },
  ]),
]);
</script>

<template>
  <MomentDetail :category="c" />
</template>
