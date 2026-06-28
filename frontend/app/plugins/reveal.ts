// Universele plugin: registreert GSAP directives voor smooth scroll animaties.
// Op de server levert getSSRProps() een no-op (anders crasht SSR). De echte
// scroll-animatie (gsap) wordt pas client-side dynamisch geladen.

let gsapLoaded = false;
let gsapInstance: typeof import('gsap').gsap | null = null;
let ScrollTriggerPlugin: typeof import('gsap/ScrollTrigger').ScrollTrigger | null = null;

async function ensureGsap() {
  if (gsapLoaded && gsapInstance && ScrollTriggerPlugin) {
    return { gsap: gsapInstance, ScrollTrigger: ScrollTriggerPlugin };
  }
  const [{ gsap }, { ScrollTrigger }] = await Promise.all([
    import('gsap'),
    import('gsap/ScrollTrigger'),
  ]);
  gsap.registerPlugin(ScrollTrigger);
  gsapInstance = gsap;
  ScrollTriggerPlugin = ScrollTrigger;
  gsapLoaded = true;
  return { gsap, ScrollTrigger };
}

export default defineNuxtPlugin((nuxtApp) => {
  // Basis reveal animatie
  nuxtApp.vueApp.directive('reveal', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;
      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) {
        el.classList.add('is-revealed');
        return;
      }

      el.classList.add('reveal');
      ensureGsap()
        .then(({ gsap }) => {
          const delay = Number(el.dataset.revealDelay ?? 0);
          const direction = el.dataset.revealDirection ?? 'up';
          const distance = Number(el.dataset.revealDistance ?? 32);

          const fromVars: gsap.TweenVars = {
            opacity: 0,
            filter: 'blur(6px)',
          };

          if (direction === 'up') fromVars.y = distance;
          else if (direction === 'down') fromVars.y = -distance;
          else if (direction === 'left') fromVars.x = distance;
          else if (direction === 'right') fromVars.x = -distance;
          else if (direction === 'scale') {
            fromVars.scale = 0.92;
            fromVars.y = distance / 2;
          }

          gsap.fromTo(el, fromVars, {
            opacity: 1,
            y: 0,
            x: 0,
            scale: 1,
            filter: 'blur(0px)',
            duration: 0.9,
            delay,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
            onStart: () => el.classList.add('is-revealed'),
          });
        })
        .catch(() => el.classList.add('is-revealed'));
    },
  });

  // Stagger animatie voor lijsten/grids
  nuxtApp.vueApp.directive('reveal-stagger', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;
      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) {
        el.querySelectorAll(':scope > *').forEach(child => child.classList.add('is-revealed'));
        return;
      }

      ensureGsap()
        .then(({ gsap }) => {
          const children = el.querySelectorAll(':scope > *');
          const staggerAmount = Number(el.dataset.stagger ?? 0.08);

          gsap.fromTo(children, {
            opacity: 0,
            y: 28,
            filter: 'blur(4px)',
          }, {
            opacity: 1,
            y: 0,
            filter: 'blur(0px)',
            duration: 0.7,
            stagger: staggerAmount,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 88%', once: true },
          });
        })
        .catch(() => {});
    },
  });

  // Parallax effect
  nuxtApp.vueApp.directive('parallax', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;
      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) return;

      ensureGsap()
        .then(({ gsap }) => {
          const speed = Number(el.dataset.parallaxSpeed ?? 0.3);
          gsap.to(el, {
            y: () => window.innerHeight * speed * -1,
            ease: 'none',
            scrollTrigger: {
              trigger: el.parentElement,
              start: 'top bottom',
              end: 'bottom top',
              scrub: true,
            },
          });
        })
        .catch(() => {});
    },
  });

  // Fade in hero (instant, no scroll trigger)
  nuxtApp.vueApp.directive('hero-reveal', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;
      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) {
        el.style.opacity = '1';
        return;
      }

      ensureGsap()
        .then(({ gsap }) => {
          const delay = Number(el.dataset.heroDelay ?? 0);
          gsap.fromTo(el, {
            opacity: 0,
            y: 24,
          }, {
            opacity: 1,
            y: 0,
            duration: 0.9,
            delay: 0.1 + delay,
            ease: 'power2.out',
          });
        })
        .catch(() => {
          el.style.opacity = '1';
        });
    },
  });

  // Text reveal (character/word animation)
  nuxtApp.vueApp.directive('text-reveal', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;
      const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
      if (reduce) return;

      ensureGsap()
        .then(({ gsap }) => {
          const text = el.textContent || '';
          const words = text.split(' ');
          el.innerHTML = words.map(word =>
            `<span class="inline-block overflow-hidden"><span class="inline-block">${word}</span></span>`
          ).join(' ');

          const spans = el.querySelectorAll('span > span');
          gsap.fromTo(spans, {
            y: '110%',
            opacity: 0,
          }, {
            y: '0%',
            opacity: 1,
            duration: 0.8,
            stagger: 0.04,
            ease: 'power3.out',
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
          });
        })
        .catch(() => {});
    },
  });

  // Smooth counter animation
  nuxtApp.vueApp.directive('counter', {
    getSSRProps: () => ({}),
    mounted(el: HTMLElement) {
      if (import.meta.server) return;

      ensureGsap()
        .then(({ gsap }) => {
          const target = Number(el.dataset.counterTarget ?? el.textContent ?? 0);
          const prefix = el.dataset.counterPrefix ?? '';
          const suffix = el.dataset.counterSuffix ?? '';
          const obj = { value: 0 };

          gsap.to(obj, {
            value: target,
            duration: 1.5,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
            onUpdate: () => {
              el.textContent = `${prefix}${Math.round(obj.value)}${suffix}`;
            },
          });
        })
        .catch(() => {});
    },
  });
});
