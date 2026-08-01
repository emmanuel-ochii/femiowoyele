<template>
  <section :class="['px-5 py-16 sm:px-6 lg:px-8 lg:py-24', toneClass]">
    <div :class="['mx-auto w-full', wide ? 'max-w-7xl' : 'max-w-6xl']">
      <slot />
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  tone: {
    type: String,
    default: 'white',
  },
  wide: {
    type: Boolean,
    default: false,
  },
});

const toneClass = computed(
  () =>
    ({
      white: 'bg-white',
      sand: 'bg-sand/70',
      navy: 'bg-navy text-white',
    })[props.tone] || 'bg-white',
);
</script>
