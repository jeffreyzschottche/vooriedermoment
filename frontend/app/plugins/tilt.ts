// v-tilt: kantelt een element in 3D mee met de muis, met een lichte glans.
// Alleen met een echte muis en zonder "reduce motion"; op touch blijft het plat.
//   <div v-tilt>            standaard max. 8 graden
//   <div v-tilt="14">       eigen maximum
export default defineNuxtPlugin((nuxtApp) => {
  type TiltEl = HTMLElement & { __tiltCleanup?: () => void };

  nuxtApp.vueApp.directive<TiltEl, number | undefined>('tilt', {
    mounted(el, binding) {
      if (typeof window === 'undefined') return;
      const fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
      const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!fine || reduced) return;

      const max = binding.value ?? 8;
      let frame = 0;
      el.classList.add('tilt');

      const onMove = (event: PointerEvent) => {
        const rect = el.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width;
        const y = (event.clientY - rect.top) / rect.height;
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(() => {
          el.style.setProperty('--tilt-x', `${(0.5 - y) * max * 2}deg`);
          el.style.setProperty('--tilt-y', `${(x - 0.5) * max * 2}deg`);
          el.style.setProperty('--glare-x', `${x * 100}%`);
          el.style.setProperty('--glare-y', `${y * 100}%`);
          el.classList.add('is-tilting');
        });
      };
      const onLeave = () => {
        cancelAnimationFrame(frame);
        el.style.setProperty('--tilt-x', '0deg');
        el.style.setProperty('--tilt-y', '0deg');
        el.classList.remove('is-tilting');
      };

      el.addEventListener('pointermove', onMove);
      el.addEventListener('pointerleave', onLeave);
      el.__tiltCleanup = () => {
        el.removeEventListener('pointermove', onMove);
        el.removeEventListener('pointerleave', onLeave);
        cancelAnimationFrame(frame);
      };
    },
    unmounted(el) {
      el.__tiltCleanup?.();
    },
    getSSRProps() {
      return {};
    },
  });
});
