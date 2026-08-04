<template>
  <RouterLink
    :to="`/work/${pillar.slug}`"
    class="group relative flex flex-col justify-between overflow-hidden border border-navy-900/10 bg-white p-7 transition-all duration-300 ease-editorial hover:-translate-y-1 hover:border-navy-900/20 hover:shadow-lift sm:p-8"
  >
    <!-- Gold wash that grows out of the top edge on hover. -->
    <span
      class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gold-500 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
    ></span>

    <div>
      <p class="index-number">{{ number }}</p>
      <h3 class="mt-6 font-serif text-2xl leading-snug text-navy-900 transition-colors duration-300 group-hover:text-forest-800">
        {{ pillar.title }}
      </h3>
      <p v-if="pillar.subtitle" class="mt-1.5 text-sm font-medium text-gold-700">{{ pillar.subtitle }}</p>
      <p v-if="pillar.description" class="mt-5 text-[0.9rem] leading-7 text-ink-muted">{{ pillar.description }}</p>
    </div>

    <span class="mt-8 inline-flex items-center gap-2 text-micro font-semibold uppercase text-navy-900/50 transition-colors duration-300 group-hover:text-forest-800">
      Explore
      <AppIcon name="arrowRight" :size="14" class="transition-transform duration-300 ease-editorial group-hover:translate-x-1" />
    </span>
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue';
import AppIcon from '../ui/AppIcon.vue';

const props = defineProps({
  pillar: { type: Object, required: true },
});

const number = computed(() => String(props.pillar.order || 0).padStart(2, '0'));
</script>
