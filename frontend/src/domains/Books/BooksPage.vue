<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Books"
      title="Authorship with a long view."
      description="Current and future works, with Entrusted positioned as the primary feature."
    />
    <SectionWrapper tone="sand">
      <div class="grid gap-6">
        <BookCard v-for="book in books" :key="book.slug" :book="book" />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import BookCard from '../../components/cards/BookCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/books');
const books = computed(() => payload.value?.data || []);
</script>
