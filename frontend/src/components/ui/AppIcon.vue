<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    :stroke-width="strokeWidth"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
  >
    <path v-for="(d, index) in paths" :key="index" :d="d" />
  </svg>
</template>

<script setup>
import { computed } from 'vue';

/** A tiny, dependency-free icon set. Stroke icons on a 24px grid. */
const ICONS = {
  arrowRight: ['M5 12h14', 'm13 6 6 6-6 6'],
  arrowUpRight: ['M7 17 17 7', 'M8 7h9v9'],
  arrowUp: ['M12 19V5', 'm6 11 6-6 6 6'],
  arrowLeft: ['M19 12H5', 'm11 18-6-6 6-6'],
  chevronDown: ['m6 9 6 6 6-6'],
  close: ['m6 6 12 12', 'm18 6-12 12'],
  menu: ['M4 7h16', 'M4 12h16', 'M4 17h16'],
  mail: ['M3 6.5h18v11H3z', 'm3 7 9 6 9-6'],
  play: ['M8 5.5v13l11-6.5z'],
  download: ['M12 4v11', 'm7.5 10.5 4.5 4.5 4.5-4.5', 'M4.5 19.5h15'],
  microphone: ['M12 3.5a2.75 2.75 0 0 1 2.75 2.75v5a2.75 2.75 0 1 1-5.5 0v-5A2.75 2.75 0 0 1 12 3.5Z', 'M5.5 11.5a6.5 6.5 0 0 0 13 0', 'M12 18v3'],
  book: ['M4 4.5h6a3 3 0 0 1 3 3v12a2.5 2.5 0 0 0-2.5-2.5H4z', 'M20 4.5h-6a3 3 0 0 0-3 3v12a2.5 2.5 0 0 1 2.5-2.5H20z'],
  check: ['m5 12.5 4.5 4.5L19 7.5'],
  alert: ['M12 8v5', 'M12 16.5h.01', 'M12 3.5 21 20H3z'],
  image: ['M3 5.5h18v13H3z', 'm3 15 5-4.5 4.5 4', 'm12 15 3-2.5L21 17', 'M15.5 9.5h.01'],
};

const props = defineProps({
  name: { type: String, required: true },
  size: { type: [Number, String], default: 18 },
  strokeWidth: { type: [Number, String], default: 1.6 },
});

const paths = computed(() => ICONS[props.name] || []);
</script>
