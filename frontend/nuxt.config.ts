export default defineNuxtConfig({
  // Nuxt 4: bron leeft in app/ (default). Geen srcDir-override meer nodig.
  compatibilityDate: '2025-01-01',

  modules: ['@pinia/nuxt'],

  // Bouwbedrijven valt nu onder Momenten; oude zakelijke URL's 301-redirecten.
  routeRules: {
    '/zakelijk/bouwbedrijven': { redirect: { to: '/momenten/bouwbedrijven', statusCode: 301 } },
    '/zakelijk': { redirect: { to: '/momenten', statusCode: 301 } },
  },

  // Laad onze eigen Tailwind CSS
  css: ['~/assets/css/tailwind.css'],

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  app: {
    head: {
      title: 'Voor Ieder Moment',
      titleTemplate: '%s | Voor Ieder Moment',
      htmlAttrs: { lang: 'nl' },
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        {
          name: 'description',
          content:
            'Een persoonlijk nummer voor elk moment: snel, betaalbaar en overal te beluisteren op Spotify en Apple Music.',
        },
        { name: 'theme-color', content: '#fdf8f0' },
        { property: 'og:type', content: 'website' },
        { property: 'og:site_name', content: 'Voor Ieder Moment' },
        { property: 'og:locale', content: 'nl_NL' },
        { property: 'og:image', content: 'https://vooriedermoment.nl/hero-studio.png' },
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:image', content: 'https://vooriedermoment.nl/hero-studio.png' },
      ],
      link: [
        { rel: 'icon', type: 'image/png', href: '/favicon-wit.png' },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..800;1,9..144,400..700&family=Manrope:wght@300;400;500;600;700;800&display=swap',
        },
      ],
    },
  },

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000/api/v1',
      // Aanbieding aan/uit. Honoreert .env `saleOn=1` (en SALE_ON / NUXT_PUBLIC_SALE_ON als alias).
      saleOn:
        (process.env.saleOn ?? process.env.SALE_ON ?? process.env.NUXT_PUBLIC_SALE_ON) === '1',
    },
  },

  devtools: { enabled: true },
});
