// JSON-LD structured data helpers. Injecteren via useHead als <script type="application/ld+json">.

export const SITE_URL = 'https://vooriedermoment.nl';

// Absolute URL voor og:image en JSON-LD-images.
export function absoluteUrl(path: string): string {
  if (/^https?:\/\//.test(path)) return path;
  return SITE_URL + (path.startsWith('/') ? path : `/${path}`);
}

export const SPOTIFY_ARTIST_URL = 'https://open.spotify.com/artist/5iXRrtThUvD2eKB4hbsGXo';

// Vaste @id's, zodat schema's op verschillende pagina's naar elkaar verwijzen.
const ORGANIZATION_ID = `${SITE_URL}/#organization`;
const WEBSITE_ID = `${SITE_URL}/#website`;
const ARTIST_ID = `${SITE_URL}/#artist`;

// Spotify-embed-URL => gewone luisterlink (open.spotify.com/album/<id>).
export function spotifyOpenUrl(embed: string): string {
  return embed.replace('/embed/', '/').split('?')[0]!;
}

export const organizationSchema = {
  '@context': 'https://schema.org',
  '@type': 'Organization',
  '@id': ORGANIZATION_ID,
  name: 'Voor Ieder Moment',
  url: SITE_URL,
  logo: { '@type': 'ImageObject', url: `${SITE_URL}/logowit.png` },
  email: 'info@vooriedermoment.nl',
  description:
    'Persoonlijke nummers op basis van jouw verhaal, uitgebracht op Spotify en YouTube Music.',
  areaServed: 'NL',
  sameAs: [SPOTIFY_ARTIST_URL],
};

export const websiteSchema = {
  '@context': 'https://schema.org',
  '@type': 'WebSite',
  '@id': WEBSITE_ID,
  name: 'Voor Ieder Moment',
  url: SITE_URL,
  inLanguage: 'nl-NL',
  publisher: { '@id': ORGANIZATION_ID },
};

// Het Spotify-artiestprofiel waaronder alle nummers verschijnen.
export const musicGroupSchema = {
  '@context': 'https://schema.org',
  '@type': 'MusicGroup',
  '@id': ARTIST_ID,
  name: 'Voor Ieder Moment',
  url: SPOTIFY_ARTIST_URL,
  sameAs: [SPOTIFY_ARTIST_URL],
  genre: 'Nederlandstalige pop',
};

export function musicAlbumSchema(opts: { name: string; embed: string; image?: string }) {
  const url = spotifyOpenUrl(opts.embed);

  return {
    '@context': 'https://schema.org',
    '@type': 'MusicAlbum',
    '@id': `${url}#album`,
    name: opts.name,
    url,
    sameAs: [url],
    ...(opts.image ? { image: absoluteUrl(opts.image) } : {}),
    byArtist: { '@type': 'MusicGroup', '@id': ARTIST_ID, name: 'Voor Ieder Moment', url: SPOTIFY_ARTIST_URL },
  };
}

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
  reviews?: { name: string; rating: number; text: string; date: string }[];
}) {
  const url = opts.url ? SITE_URL + opts.url : SITE_URL;
  const reviews = opts.reviews ?? [];

  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    '@id': `${url}#product`,
    name: opts.name,
    description: opts.description,
    ...(opts.image ? { image: absoluteUrl(opts.image) } : {}),
    brand: { '@type': 'Brand', name: 'Voor Ieder Moment' },
    offers: {
      '@type': 'Offer',
      price: opts.price.toFixed(2),
      priceCurrency: 'EUR',
      availability: 'https://schema.org/InStock',
      url,
      seller: { '@id': ORGANIZATION_ID },
    },
    // Alleen echte reviews (zie data/reviews.ts); zonder reviews geen rating.
    ...(reviews.length
      ? {
          aggregateRating: {
            '@type': 'AggregateRating',
            ratingValue: (reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length).toFixed(1),
            reviewCount: reviews.length,
            bestRating: 5,
          },
          review: reviews.map((r) => ({
            '@type': 'Review',
            author: { '@type': 'Person', name: r.name },
            datePublished: r.date,
            reviewBody: r.text,
            reviewRating: { '@type': 'Rating', ratingValue: r.rating, bestRating: 5 },
          })),
        }
      : {}),
  };
}

// De dienst zelf, met per moment een aanbod. Helpt zoekmachines en LLM's te
// begrijpen wát we doen, voor wie en tegen welke prijs.
export function serviceSchema(opts: {
  price: number;
  moments: { name: string; path: string; description: string }[];
}) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Service',
    '@id': `${SITE_URL}/#service`,
    name: 'Persoonlijk nummer laten maken',
    serviceType: 'Persoonlijk Nederlandstalig lied op maat',
    description:
      'Een persoonlijk Nederlandstalig nummer op basis van jouw verhaal: je ontvangt direct na betaling je songtekst, kiest uit verschillende muzikale voorproefjes en wij publiceren de favoriet op Spotify en YouTube Music.',
    provider: { '@id': ORGANIZATION_ID },
    areaServed: { '@type': 'Country', name: 'Nederland' },
    availableLanguage: 'nl',
    url: SITE_URL,
    offers: {
      '@type': 'Offer',
      price: opts.price.toFixed(2),
      priceCurrency: 'EUR',
      url: `${SITE_URL}/aanvraag`,
    },
    hasOfferCatalog: {
      '@type': 'OfferCatalog',
      name: 'Momenten',
      itemListElement: opts.moments.map((m) => ({
        '@type': 'Offer',
        price: opts.price.toFixed(2),
        priceCurrency: 'EUR',
        url: SITE_URL + m.path,
        itemOffered: { '@type': 'Service', name: m.name, description: m.description, url: SITE_URL + m.path },
      })),
    },
  };
}

export function itemListSchema(items: { name: string; path: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    itemListElement: items.map((it, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: it.name,
      url: SITE_URL + it.path,
    })),
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
