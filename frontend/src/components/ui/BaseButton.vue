<template>
  <component
    :is="to ? RouterLink : 'button'"
    :to="to"
    :type="to ? undefined : type"
    :class="classes"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';

const props = defineProps({
  to: {
    type: [String, Object],
    default: null,
  },
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary',
  },
});

const variants = {
  primary: 'bg-navy text-white hover:bg-forest',
  secondary: 'border border-navy/20 bg-white text-navy hover:border-forest hover:text-forest',
  ghost: 'text-navy hover:text-forest',
  gold: 'bg-gold text-navy hover:bg-[#d6b872]',
};

const classes = computed(() => [
  'focus-ring inline-flex min-h-11 items-center justify-center gap-2 rounded-sm px-5 py-2.5 text-sm font-semibold transition duration-200',
  variants[props.variant] || variants.primary,
]);
</script>
