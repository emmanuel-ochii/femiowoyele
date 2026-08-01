<template>
  <section class="bg-navy px-5 py-16 text-white sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
      <p class="font-serif text-3xl leading-tight sm:text-4xl">“{{ quote?.text }}”</p>
      <p v-if="quote?.source" class="mt-6 text-sm font-semibold text-warmgrey">{{ quote.source }}</p>
    </div>
  </section>
</template>

<script setup>
defineProps({
  quote: {
    type: Object,
    default: null,
  },
});
</script>
