<template>
  <article :class="wrapperClass">
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
      <p v-if="meta" class="text-micro font-semibold uppercase text-forest-700">{{ meta }}</p>
      <span v-if="meta && date" class="h-1 w-1 rounded-full bg-gold-500"></span>
      <p v-if="date" class="text-micro font-medium uppercase text-ink-faint">{{ date }}</p>
    </div>

    <h3 :class="['font-serif leading-snug text-navy-900', variant === 'card' ? 'mt-4 text-xl' : 'mt-3 text-2xl']">
      <component
        :is="linkTag"
        v-if="linkTag"
        v-bind="linkAttrs"
        class="transition-colors duration-300 before:absolute before:inset-0 before:content-[''] hover:text-forest-800"
      >
        {{ title }}
      </component>
      <span v-else>{{ title }}</span>
    </h3>

    <p v-if="description" class="mt-3 line-clamp-4 text-[0.9rem] leading-7 text-ink-muted">{{ description }}</p>

    <div v-if="linkTag" class="mt-auto pt-6">
      <span class="inline-flex items-center gap-2 text-micro font-semibold uppercase text-navy-900/45 transition-colors duration-300 group-hover:text-forest-800">
        {{ action }}
        <AppIcon name="arrowRight" :size="14" class="transition-transform duration-300 ease-editorial group-hover:translate-x-1" />
      </span>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import AppIcon from '../ui/AppIcon.vue';

const props = defineProps({
  to: { type: [String, Object], default: null },
  href: { type: String, default: null },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  meta: { type: String, default: '' },
  date: { type: String, default: '' },
  action: { type: String, default: 'Read' },
  /** card | list */
  variant: { type: String, default: 'card' },
});

const linkTag = computed(() => (props.to ? RouterLink : props.href ? 'a' : null));

const linkAttrs = computed(() => {
  if (props.to) return { to: props.to };
  if (props.href) return { href: props.href, target: '_blank', rel: 'noopener noreferrer' };
  return {};
});

const wrapperClass = computed(() => [
  'group relative flex h-full flex-col',
  props.variant === 'card'
    ? 'border border-navy-900/10 bg-white p-7 transition-all duration-300 ease-editorial hover:-translate-y-1 hover:border-navy-900/20 hover:shadow-lift'
    : 'border-t border-navy-900/12 py-7 transition-colors duration-300 hover:border-navy-900/30',
]);
</script>
