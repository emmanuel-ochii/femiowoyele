<template>
  <section :class="['px-5 pt-28 sm:px-6 lg:px-8', image ? 'pb-12' : 'pb-16']">
    <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
      <div class="animate-floatIn">
        <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>
        <h1 class="heading-lg mt-4 text-navy">{{ title }}</h1>
        <p v-if="description" class="mt-6 max-w-2xl text-lg leading-8 text-ink/72">{{ description }}</p>
      </div>
      <div v-if="image" class="h-[280px] overflow-hidden rounded-sm sm:h-[360px] lg:h-[440px]">
        <img :src="image" :alt="imageAlt" class="h-full w-full object-cover" />
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  eyebrow: {
    type: String,
    default: '',
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  image: {
    type: String,
    default: '',
  },
  imageAlt: {
    type: String,
    default: '',
  },
});
</script>
