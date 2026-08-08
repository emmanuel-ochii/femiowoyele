<template>
  <p
    class="flex flex-wrap items-center gap-x-3 gap-y-1 border-l-2 border-gold-500 bg-gold-500/12 px-4 py-3 text-[0.85rem] leading-6 text-gold-200"
    role="status"
  >
    <span class="text-micro font-semibold uppercase tracking-[0.14em] text-gold-400">Test mode</span>
    <span>{{ message }}</span>
  </p>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    // default: 'Payments are simulated. No card is charged and no money changes hands.',
  },
});
</script>
