<template>
  <article class="group border-t border-navy/12 py-6 transition duration-200 hover:border-forest">
    <p v-if="meta" class="text-sm font-semibold text-forest">{{ meta }}</p>
    <h3 class="mt-2 font-serif text-2xl leading-tight text-navy">
      <RouterLink v-if="to" :to="to" class="focus-ring hover:text-forest">{{ title }}</RouterLink>
      <span v-else>{{ title }}</span>
    </h3>
    <p v-if="description" class="mt-4 text-sm leading-7 text-ink/70">{{ description }}</p>
  </article>
</template>

<script setup>
defineProps({
  to: {
    type: [String, Object],
    default: null,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  meta: {
    type: String,
    default: '',
  },
});
</script>
