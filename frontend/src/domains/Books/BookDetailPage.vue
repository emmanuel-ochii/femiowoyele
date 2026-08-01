<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero :eyebrow="book.is_featured ? 'Featured Book' : 'Book'" :title="book.title" :description="book.subtitle" />
    <SectionWrapper>
      <div class="grid gap-10 lg:grid-cols-[260px_1fr]">
        <img :src="book.cover_image_path || '/images/entrusted-cover.svg'" :alt="`${book.title} cover`" class="w-full max-w-[260px] shadow-soft" />
        <div class="prose-content max-w-3xl">
          <p>{{ book.description }}</p>
          <BaseButton to="/contact" class="mt-8">Make an enquiry</BaseButton>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import PageHero from '../../components/sections/PageHero.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const route = useRoute();
const { payload, loading, error } = useApiPage(() => `/books/${route.params.slug}`);
const book = computed(() => payload.value?.data || {});
</script>
