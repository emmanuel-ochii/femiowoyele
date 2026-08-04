<template>
  <div class="border border-dashed border-navy-900/15 bg-sand-50 px-8 py-16 text-center">
    <p class="display-4 text-navy-900">{{ title }}</p>
    <p class="body-copy mx-auto mt-3 max-w-md">{{ message }}</p>
    <slot />
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: 'Nothing published here yet.' },
  message: {
    type: String,
    default: 'New material is added as the work develops. Subscribe below to be notified when it arrives.',
  },
});
</script>
