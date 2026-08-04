<template>
  <div v-reveal :class="['flex flex-col gap-6', align === 'between' && 'md:flex-row md:items-end md:justify-between']">
    <div :class="['max-w-2xl', align === 'center' && 'mx-auto text-center']">
      <p v-if="eyebrow" :class="['eyebrow', align === 'center' && 'justify-center', dark && '!text-gold-400']">
        {{ eyebrow }}
      </p>
      <component :is="as" :class="['mt-5 text-balance', level, dark ? 'text-white' : 'text-navy-900']">
        {{ title }}
      </component>
      <p v-if="lead" :class="['lead mt-5 text-pretty', dark && '!text-white/70']">{{ lead }}</p>
      <slot name="lead" />
    </div>
    <div v-if="$slots.action" class="shrink-0">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  eyebrow: { type: String, default: '' },
  title: { type: String, required: true },
  lead: { type: String, default: '' },
  /** Heading element — keep the document outline sensible per page. */
  as: { type: String, default: 'h2' },
  /** Utility class for the display size. */
  level: { type: String, default: 'display-3' },
  /** left | center | between */
  align: { type: String, default: 'left' },
  dark: { type: Boolean, default: false },
});
</script>
