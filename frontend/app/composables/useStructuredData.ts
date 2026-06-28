// JSON-LD structured data helpers. Injecteren via useHead als <script type="application/ld+json">.

export const SITE_URL = 'https://vooriedermoment.nl';

// Absolute URL voor og:image en JSON-LD-images.
export function absoluteUrl(path: string): string {
  if (/^https?:\/\//.test(path)) return path;
  return SITE_URL + (path.startsWith('/') ? path : `/${path}`);
}

export const organizationSchema = {
  '@context': 'https://schema.org',
  '@type': 'Organization',
  name: 'Voor Ieder Moment',
  url: SITE_URL,
  logo: `${SITE_URL}/faviconzwart.png`,
  email: 'info@vooriedermoment.nl',
  description:
    'Persoonlijke, AI-gegenereerde nummers voor elk moment — overal te beluisteren op Spotify en Apple Music.',
  address: {
    '@type': 'PostalAddress',
    addressLocality: 'Amsterdam',
    addressCountry: 'NL',
  },
};

export function useJsonLd(schema: Record<string, any> | Record<string, any>[]) {
  const items = Array.isArray(schema) ? schema : [schema];
  useHead({
    script: items.map((item) => ({
      type: 'application/ld+json',
      innerHTML: JSON.stringify(item),
    })),
  });
}

export function productSchema(opts: {
  name: string;
  description: string;
  price: number;
  url?: string;
  image?: string;
}) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: opts.name,
    description: opts.description,
    ...(opts.image ? { image: absoluteUrl(opts.image) } : {}),
    brand: { '@type': 'Brand', name: 'Voor Ieder Moment' },
    offers: {
      '@type': 'Offer',
      price: opts.price.toFixed(2),
      priceCurrency: 'EUR',
      availability: 'https://schema.org/InStock',
      url: opts.url ? SITE_URL + opts.url : SITE_URL,
    },
  };
}

export function faqSchema(faqs: { question: string; answer: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((f) => ({
      '@type': 'Question',
      name: f.question,
      acceptedAnswer: { '@type': 'Answer', text: f.answer },
    })),
  };
}

export function breadcrumbSchema(items: { name: string; path: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((it, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: it.name,
      item: SITE_URL + it.path,
    })),
  };
}
