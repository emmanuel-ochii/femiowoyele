<template>
  <div class="shell py-24 lg:py-32">
    <div class="max-w-xl border-t-2 border-gold-500 bg-sand-50 p-8 sm:p-10">
      <p class="eyebrow-plain text-gold-700">Temporarily unavailable</p>
      <h1 class="display-4 mt-4 text-navy-900">{{ title }}</h1>
      <p class="body-copy mt-4">{{ message }}</p>
      <div class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-4">
        <BaseButton v-if="onRetry" variant="primary" icon="arrowRight" @click="onRetry">Try again</BaseButton>
        <BaseButton to="/" variant="ghost">Return to the homepage</BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseButton from './BaseButton.vue';

defineProps({
  title: { type: String, default: 'This section could not be loaded.' },
  message: {
    type: String,
    default: 'Something interrupted the connection while the page was loading. Please try again in a moment.',
  },
  /** Optional retry handler — the button is hidden when none is supplied. */
  onRetry: { type: Function, default: null },
});
</script>
