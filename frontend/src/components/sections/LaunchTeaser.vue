<template>
  <RouterLink
    v-if="launch"
    to="/entrusted"
    class="group relative flex items-center gap-5 overflow-hidden border border-gold-500/40 bg-navy-950/55 p-4 backdrop-blur-sm transition-all duration-300 ease-editorial hover:border-gold-400 hover:bg-navy-950/75 sm:gap-6 sm:p-5"
  >
    <!-- Gold sweep on hover: enough motion to read as a live announcement. -->
    <span
      aria-hidden="true"
      class="pointer-events-none absolute inset-y-0 -left-full w-full bg-gradient-to-r from-transparent via-gold-500/12 to-transparent transition-transform duration-700 ease-editorial group-hover:translate-x-[200%]"
    ></span>

    <span class="relative shrink-0 bg-white p-1 shadow-lift">
      <img
        :src="image"
        :alt="`Cover of ${title}`"
        class="h-20 w-auto object-contain mix-blend-multiply sm:h-24"
        loading="lazy"
        decoding="async"
      />
    </span>

    <span class="relative min-w-0 flex-1">
      <span class="flex flex-wrap items-center gap-x-3 gap-y-1">
        <span class="text-micro font-semibold uppercase text-gold-400">{{ statusLabel }}</span>
      </span>

      <span class="mt-2 block font-serif text-xl leading-tight text-white sm:text-2xl">{{ title }}</span>
      <span class="mt-1.5 block truncate text-[0.82rem] text-white/60">{{ occasion }}</span>
    </span>

    <AppIcon
      name="arrowRight"
      :size="18"
      class="relative shrink-0 text-gold-400 transition-transform duration-300 ease-editorial group-hover:translate-x-1"
    />
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue';
import AppIcon from '../ui/AppIcon.vue';

const props = defineProps({
  launch: { type: Object, default: null },
});

const meta = computed(() => props.launch?.meta || {});
const title = computed(() => props.launch?.title || 'Entrusted');
const image = computed(() => meta.value.image || '/images/entrusted-mock-sm.jpg');
const occasion = computed(() => meta.value.occasion || '');

const statusLabel = computed(() => 'Featured book');
</script>
