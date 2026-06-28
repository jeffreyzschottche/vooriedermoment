<script setup lang="ts">
import { getCategory, categoryImage } from '~/data/categories';
import { absoluteUrl } from '~/composables/useStructuredData';

const c = getCategory('bouwbedrijven')!;
const offer = useOffer();
const cover = absoluteUrl(categoryImage(c));
const pageUrl = absoluteUrl(c.route ?? '/zakelijk/bouwbedrijven');

useSeoMeta({
  title: c.seoTitle,
  ogTitle: c.seoTitle,
  description: c.seoDescription,
  ogDescription: c.seoDescription,
  ogType: 'website',
  ogUrl: pageUrl,
  ogImage: cover,
  twitterCard: 'summary_large_image',
  twitterTitle: c.seoTitle,
  twitterDescription: c.seoDescription,
  twitterImage: cover,
});

useHead({ link: [{ rel: 'canonical', href: pageUrl }] });

useJsonLd([
  productSchema({ name: 'Bedrijfsnummer voor bouwbedrijven', description: c.seoDescription, price: offer.currentPrice.value, url: c.route, image: categoryImage(c) }),
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
