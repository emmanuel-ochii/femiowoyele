/**
 * `v-reveal` — fades a block in the first time it enters the viewport.
 *
 * Usage:
 *   <div v-reveal />          fade in immediately on intersect
 *   <div v-reveal="120" />    stagger by 120ms
 *
 * Anything that cannot observe (SSR, jsdom, reduced motion) is shown at once so
 * content is never trapped behind an effect that will not run.
 */
const REVEALED = 'is-visible';

let observer = null;

function getObserver() {
  if (observer) return observer;

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add(REVEALED);
        observer.unobserve(entry.target);
      });
    },
    { rootMargin: '0px 0px -12% 0px', threshold: 0.08 },
  );

  return observer;
}

export const reveal = {
  mounted(el, binding) {
    const prefersReducedMotion =
      typeof window !== 'undefined' &&
      typeof window.matchMedia === 'function' &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (typeof IntersectionObserver === 'undefined' || prefersReducedMotion) {
      el.classList.add('reveal', REVEALED);
      return;
    }

    const delay = Number(binding.value) || 0;
    if (delay) el.style.setProperty('--reveal-delay', `${delay}ms`);

    el.classList.add('reveal');
    getObserver().observe(el);

    // Failsafe: content must never stay invisible because an observer callback
    // did not run (print, headless capture, background tabs).
    el._revealFailsafe = setTimeout(() => {
      el.classList.add(REVEALED);
      observer?.unobserve(el);
    }, 1200);
  },

  unmounted(el) {
    clearTimeout(el._revealFailsafe);
    observer?.unobserve(el);
  },
};

export default reveal;
