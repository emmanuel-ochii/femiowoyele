<template>
  <article class="grid gap-6 border border-navy/10 bg-white p-5 sm:grid-cols-[130px_1fr]">
    <img
      :src="book.cover_image_path || '/images/entrusted-cover.svg'"
      :alt="`${book.title} cover`"
      class="aspect-[2/3] w-full max-w-[180px] rounded-sm object-cover shadow-soft"
      loading="lazy"
    />
    <div>
      <p v-if="book.is_featured" class="eyebrow">Featured</p>
      <h3 class="mt-2 font-serif text-3xl text-navy">
        <RouterLink :to="`/books/${book.slug}`" class="focus-ring hover:text-forest">{{ book.title }}</RouterLink>
      </h3>
      <p v-if="book.subtitle" class="mt-2 text-sm font-semibold text-forest">{{ book.subtitle }}</p>
      <p class="mt-4 text-sm leading-7 text-ink/70">{{ book.description }}</p>
    </div>
  </article>
</template>

<script setup>
defineProps({
  book: {
    type: Object,
    required: true,
  },
});
</script>
