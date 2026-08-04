<template>
  <component
    :is="tag"
    v-bind="linkAttrs"
    :type="isLink ? undefined : type"
    :disabled="isLink ? undefined : disabled || loading"
    :aria-busy="loading ? 'true' : undefined"
    :class="classes"
  >
    <slot />
    <AppIcon
      v-if="icon"
      :name="icon"
      :size="16"
      class="shrink-0 transition-transform duration-300 ease-editorial group-hover:translate-x-0.5"
    />
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import AppIcon from './AppIcon.vue';

const props = defineProps({
  /** Internal route — renders a RouterLink. */
  to: { type: [String, Object], default: null },
  /** External URL — renders an anchor with safe rel attributes. */
  href: { type: String, default: null },
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  icon: { type: String, default: null },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
});

const VARIANTS = {
  primary: 'bg-navy-900 text-white hover:bg-forest-800 active:bg-forest-900',
  gold: 'bg-gold-500 text-navy-900 hover:bg-gold-400 active:bg-gold-600',
  outline: 'border border-navy-900/25 text-navy-900 hover:border-forest-800 hover:bg-forest-800 hover:text-white',
  'outline-light': 'border border-white/35 text-white hover:border-white hover:bg-white hover:text-navy-900',
  ghost:
    'text-navy-900 underline decoration-gold-400 decoration-1 underline-offset-[6px] hover:text-forest-700 hover:decoration-forest-600',
  'ghost-light': 'text-white underline decoration-gold-400 decoration-1 underline-offset-[6px] hover:text-gold-300',
};

const SIZES = {
  sm: 'min-h-9 px-4 text-[0.8rem]',
  md: 'min-h-11 px-6 text-[0.85rem]',
  lg: 'min-h-[3.25rem] px-8 text-[0.9rem]',
};

const GHOST_SIZES = {
  sm: 'min-h-9 text-[0.8rem]',
  md: 'min-h-11 text-[0.85rem]',
  lg: 'min-h-[3.25rem] text-[0.9rem]',
};

const isLink = computed(() => Boolean(props.to || props.href));
const tag = computed(() => (props.to ? RouterLink : props.href ? 'a' : 'button'));
const isGhost = computed(() => props.variant.startsWith('ghost'));

const linkAttrs = computed(() => {
  if (props.to) return { to: props.to };
  if (!props.href) return {};
  // In-page anchors and mailto/tel links must stay in the current tab.
  if (/^[#]|^(mailto|tel):/.test(props.href)) return { href: props.href };
  return { href: props.href, target: '_blank', rel: 'noopener noreferrer' };
});

const classes = computed(() => [
  'group inline-flex items-center justify-center gap-2.5 rounded-sm font-semibold tracking-[0.02em]',
  'transition-all duration-300 ease-editorial disabled:cursor-not-allowed disabled:opacity-45',
  (isGhost.value ? GHOST_SIZES : SIZES)[props.size] || (isGhost.value ? GHOST_SIZES.md : SIZES.md),
  VARIANTS[props.variant] || VARIANTS.primary,
  props.loading && 'cursor-progress opacity-70',
]);
</script>
