<template>
  <article
    class="group relative grid gap-8 border border-navy-900/10 bg-white p-6 transition-all duration-300 ease-editorial hover:border-navy-900/20 hover:shadow-lift sm:grid-cols-[150px_1fr] sm:p-8 lg:grid-cols-[180px_1fr] lg:gap-10"
  >
    <BookCover :book="book" class="max-w-[180px]" />

    <div class="flex flex-col">
      <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
        <p v-if="book.is_featured" class="text-micro font-semibold uppercase text-gold-700">Featured work</p>
        <p v-else class="text-micro font-semibold uppercase text-ink-faint">{{ book.status || 'In progress' }}</p>
      </div>

      <h3 class="mt-4 font-serif text-3xl leading-tight text-navy-900">
        <RouterLink
          :to="`/books/${book.slug}`"
          class="transition-colors duration-300 before:absolute before:inset-0 before:content-[''] hover:text-forest-800"
        >
          {{ book.title }}
        </RouterLink>
      </h3>

      <p v-if="book.subtitle" class="mt-2.5 text-[0.95rem] font-medium text-forest-800">{{ book.subtitle }}</p>
      <p v-if="book.description" class="mt-5 text-[0.9rem] leading-7 text-ink-muted">{{ book.description }}</p>

      <span class="mt-auto pt-7 inline-flex items-center gap-2 text-micro font-semibold uppercase text-navy-900/45 transition-colors duration-300 group-hover:text-forest-800">
        About the book
        <AppIcon name="arrowRight" :size="14" class="transition-transform duration-300 ease-editorial group-hover:translate-x-1" />
      </span>
    </div>
  </article>
</template>

<script setup>
import AppIcon from '../ui/AppIcon.vue';
import BookCover from './BookCover.vue';

defineProps({
  book: { type: Object, required: true },
});
</script>
