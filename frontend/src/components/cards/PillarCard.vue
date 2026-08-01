<template>
  <RouterLink
    :to="`/work/${pillar.slug}`"
    class="focus-ring group block border border-navy/10 bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-forest hover:shadow-soft"
  >
    <p class="text-sm font-semibold text-forest">{{ String(pillar.order || '').padStart(2, '0') }}</p>
    <h3 class="mt-6 font-serif text-2xl text-navy">{{ pillar.title }}</h3>
    <p class="mt-2 text-sm font-semibold text-ink/60">{{ pillar.subtitle }}</p>
    <p class="mt-5 text-sm leading-7 text-ink/70">{{ pillar.description }}</p>
  </RouterLink>
</template>

<script setup>
defineProps({
  pillar: {
    type: Object,
    required: true,
  },
});
</script>
