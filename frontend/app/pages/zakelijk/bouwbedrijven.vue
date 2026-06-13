<script setup lang="ts">
import { getCategory } from '~/data/categories';

const c = getCategory('bouwbedrijven')!;
const offer = useOffer();

useSeoMeta({
  title: c.seoTitle,
  ogTitle: c.seoTitle,
  description: c.seoDescription,
  ogDescription: c.seoDescription,
});

useJsonLd([
  productSchema({ name: 'Bedrijfsnummer voor bouwbedrijven', description: c.seoDescription, price: offer.currentPrice.value, url: c.route }),
  faqSchema(c.faq),
  breadcrumbSchema([
    { name: 'Home', path: '/' },
    { name: 'Zakelijk', path: '/zakelijk/bouwbedrijven' },
    { name: 'Bouwbedrijven', path: '/zakelijk/bouwbedrijven' },
  ]),
]);
</script>

<template>
  <MomentDetail :category="c" />
</template>
