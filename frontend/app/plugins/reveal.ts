// Universele plugin: registreert de v-reveal directive op zowel server als client.
// Op de server levert getSSRProps() een no-op (anders crasht SSR). De echte
// scroll-animatie (gsap) wordt pas client-side dynamisch geladen.
export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.directive('reveal', {
    // Voorkomt de SSR-fout "Cannot read properties of undefined (reading 'getSSRProps')".
    getSSRProps: () => ({}),

    mounted(el: HTMLElement) {
      if (import.meta.server) return;

      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) {
        el.classList.add('is-revealed');
        return;
      }

      el.classList.add('reveal');
      Promise.all([import('gsap'), import('gsap/ScrollTrigger')])
        .then(([{ gsap }, { ScrollTrigger }]) => {
          gsap.registerPlugin(ScrollTrigger);
          const delay = Number(el.dataset.revealDelay ?? 0);
          gsap.fromTo(el, {
            opacity: 0,
            y: 24,
            filter: 'blur(10px)',
          }, {
            opacity: 1,
            y: 0,
            filter: 'blur(0px)',
            duration: 0.8,
            delay,
            ease: 'power3.out',
            scrollTrigger: { trigger: el, start: 'top 88%', once: true },
            onStart: () => el.classList.add('is-revealed'),
          });
        })
        .catch(() => el.classList.add('is-revealed'));
    },
  });
});
