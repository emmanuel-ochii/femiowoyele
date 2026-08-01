<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Journal"
      title="Essays, reflections, and notes from the work."
      description="Shorter reflections and insights that keep the body of work alive between major publications."
    />
    <SectionWrapper>
      <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
        <ContentCard
          v-for="entry in entries"
          :key="entry.slug"
          :to="`/journal/${entry.slug}`"
          :title="entry.title"
          :description="entry.excerpt"
          :meta="entry.published_at"
        />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/journal');
const entries = computed(() => payload.value?.data || []);
</script>
