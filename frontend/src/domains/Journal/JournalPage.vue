<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Journal"
      title="Notes taken while the work is happening."
      description="Shorter reflections, field notes, and observations — the thinking that eventually becomes essays and books."
    />

    <SectionWrapper>
      <div v-if="entries.length" :class="['grid gap-6', cardGridClass(entries.length)]">
        <ContentCard
          v-for="(entry, index) in entries"
          :key="entry.slug"
          v-reveal="index * 50"
          :to="`/journal/${entry.slug}`"
          :title="entry.title"
          :description="entry.excerpt"
          :meta="entry.category?.name || 'Journal'"
          :date="formatDate(entry.published_at)"
          action="Read the note"
        />
      </div>

      <EmptyState v-else title="The first entries are on their way." />
    </SectionWrapper>

    <CtaBand
      eyebrow="Go deeper"
      title="The longer arguments live in Research & Ideas."
      description="Essays and frameworks written for people carrying real responsibility."
      primary-to="/research-ideas"
      primary-label="Read the essays"
      secondary-to="/books"
      secondary-label="Books"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate } from '../../utils/format';
import { cardGridClass } from '../../utils/layout';

const { payload, loading, error, retry } = useApiPage('/journal');
const entries = computed(() => payload.value?.data || []);

usePageMeta({
  title: 'Journal',
  description: 'Shorter reflections, field notes, and observations from the work of Femi Owoyele.',
});
</script>
