<template>
  <section :id="id" :class="[toneClass, paddingClass, 'relative']">
    <div :class="['shell relative', narrow && 'max-w-5xl']">
      <slot />
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  /** white | sand | navy | forest | paper */
  tone: { type: String, default: 'white' },
  /** sm | md | lg — vertical rhythm. */
  space: { type: String, default: 'md' },
  narrow: { type: Boolean, default: false },
  id: { type: String, default: undefined },
});

const TONES = {
  white: 'bg-white text-ink',
  paper: 'bg-sand-50 text-ink',
  sand: 'surface-sand',
  navy: 'surface-navy on-dark',
  forest: 'bg-forest-900 text-white on-dark',
};

const SPACING = {
  sm: 'py-12 sm:py-16',
  md: 'py-16 sm:py-20 lg:py-28',
  lg: 'py-20 sm:py-28 lg:py-36',
};

const toneClass = computed(() => TONES[props.tone] || TONES.white);
const paddingClass = computed(() => SPACING[props.space] || SPACING.md);
</script>
