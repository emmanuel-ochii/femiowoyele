<template>
  <div class="border-l border-gold pl-5">
    <p class="font-serif text-4xl text-navy">{{ metric.value }}</p>
    <p class="mt-2 text-sm font-semibold text-forest">{{ metric.label }}</p>
    <p class="mt-3 text-sm leading-6 text-ink/65">{{ metric.description }}</p>
  </div>
</template>

<script setup>
defineProps({
  metric: {
    type: Object,
    required: true,
  },
});
</script>
