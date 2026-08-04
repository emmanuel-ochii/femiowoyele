import { computed, onBeforeUnmount, onMounted, ref, unref } from 'vue';

/**
 * Counts down to an ISO date string.
 *
 * Ticks once a minute rather than once a second: the display stops at minutes,
 * so a faster interval would only burn wake-ups. `hasPassed` lets callers swap
 * an upcoming-event layout for an after-the-fact one instead of showing zeros.
 */
export function useCountdown(target) {
  const now = ref(Date.now());
  let timer = null;

  const targetTime = computed(() => {
    // Accept a plain value, a ref, or a getter, matching the other composables.
    const value = typeof target === 'function' ? target() : unref(target);
    if (!value) return null;
    const parsed = new Date(value).getTime();
    return Number.isNaN(parsed) ? null : parsed;
  });

  const remaining = computed(() => (targetTime.value === null ? null : targetTime.value - now.value));
  const hasPassed = computed(() => remaining.value !== null && remaining.value <= 0);

  const parts = computed(() => {
    const ms = Math.max(0, remaining.value ?? 0);
    const totalMinutes = Math.floor(ms / 60000);

    return {
      days: Math.floor(totalMinutes / (60 * 24)),
      hours: Math.floor((totalMinutes % (60 * 24)) / 60),
      minutes: totalMinutes % 60,
    };
  });

  /** True on the day of the event but before it starts. */
  const isToday = computed(() => !hasPassed.value && parts.value.days === 0);

  const tick = () => {
    now.value = Date.now();
  };

  onMounted(() => {
    tick();
    timer = setInterval(tick, 60_000);
  });

  onBeforeUnmount(() => clearInterval(timer));

  return { parts, remaining, hasPassed, isToday };
}

export default useCountdown;
