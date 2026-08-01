<template>
  <div class="mx-auto max-w-6xl px-5 py-24 text-center text-ink/60">
    <div class="mx-auto mb-5 h-px w-20 bg-gold"></div>
    <p>{{ label }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    default: 'Loading content...',
  },
});
</script>
