<template>
  <div>
    <PageHero
      eyebrow="Research &amp; Ideas"
      title="Essays for people carrying real responsibility."
      description="Frameworks and arguments on enterprise, governance, sustainability, and leadership — written to be useful long after the news cycle has moved on."
    />

    <SectionWrapper>
      <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <FilterBar v-model="category" :options="filterOptions" label="Filter essays by category" class="flex-1" />
        <p class="shrink-0 text-micro font-semibold uppercase text-ink-faint sm:pb-5">
          {{ articles.length }} {{ articles.length === 1 ? 'essay' : 'essays' }}
        </p>
      </div>

      <LoadingState v-if="loading" class="!px-0" />
      <ErrorState v-else-if="error" :on-retry="retry" class="!px-0" />

      <div v-else-if="articles.length" :class="['mt-12 grid gap-6', cardGridClass(articles.length)]">
        <ContentCard
          v-for="(article, index) in articles"
          :key="article.slug"
          v-reveal="index * 50"
          :to="`/research-ideas/${article.slug}`"
          :title="article.title"
          :description="article.excerpt"
          :meta="article.category?.name"
          :date="formatDate(article.published_at)"
          action="Read the essay"
        />
      </div>

      <EmptyState
        v-else
        class="mt-12"
        title="No essays in this category yet."
        message="Try another category, or subscribe to be notified when new writing is published."
      />
    </SectionWrapper>

    <CtaBand
      eyebrow="Keep reading"
      title="Shorter reflections live in the Journal."
      description="Notes and observations between the longer essays."
      primary-to="/journal"
      primary-label="Read the Journal"
      secondary-to="/books"
      secondary-label="Books"
    />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import FilterBar from '../../components/ui/FilterBar.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate } from '../../utils/format';
import { cardGridClass } from '../../utils/layout';

const category = ref('');
const { payload, loading, error, load, retry } = useApiPage('/research-ideas', () =>
  category.value ? { category: category.value } : {},
);

watch(category, () => load());

const articles = computed(() => payload.value?.data || []);
const categories = computed(() => payload.value?.meta?.categories || []);
const filterOptions = computed(() => [
  { label: 'All', value: '' },
  ...categories.value.map((item) => ({ label: item.name, value: item.slug })),
]);

usePageMeta({
  title: 'Research & Ideas',
  description:
    'Essays and frameworks on enterprise, governance, sustainability, and leadership by Femi Owoyele.',
});
</script>
