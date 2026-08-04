<template>
  <div :class="['relative pt-6', dark ? 'border-t border-white/20' : 'border-t border-navy-900/15']">
    <span class="absolute left-0 top-0 h-px w-12 bg-gold-500"></span>
    <p :class="['font-serif text-[clamp(2.5rem,1.5rem+2.6vw,3.75rem)] leading-none tabular-nums', dark ? 'text-white' : 'text-navy-900']">
      {{ metric.value }}
    </p>
    <p :class="['mt-4 text-[0.9rem] font-semibold', dark ? 'text-gold-400' : 'text-forest-800']">{{ metric.label }}</p>
    <p v-if="metric.description" :class="['mt-2.5 text-sm leading-6', dark ? 'text-white/60' : 'text-ink-muted']">
      {{ metric.description }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  metric: { type: Object, required: true },
  dark: { type: Boolean, default: false },
});
</script>
