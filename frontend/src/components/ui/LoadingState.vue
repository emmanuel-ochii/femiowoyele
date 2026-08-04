<template>
  <div class="shell py-24 lg:py-32" role="status" aria-live="polite">
    <span class="sr-only">{{ label }}</span>
    <div class="max-w-2xl">
      <div class="skeleton h-3 w-24"></div>
      <div class="skeleton mt-7 h-11 w-full"></div>
      <div class="skeleton mt-3 h-11 w-4/5"></div>
      <div class="skeleton mt-8 h-4 w-full"></div>
      <div class="skeleton mt-3 h-4 w-11/12"></div>
      <div class="skeleton mt-3 h-4 w-3/5"></div>
    </div>
    <div class="mt-16 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="n in 3" :key="n" class="border border-navy-900/10 p-7">
        <div class="skeleton h-3 w-10"></div>
        <div class="skeleton mt-6 h-6 w-2/3"></div>
        <div class="skeleton mt-5 h-3 w-full"></div>
        <div class="skeleton mt-2.5 h-3 w-5/6"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, default: 'Loading' },
});
</script>
