<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero eyebrow="Impact" title="An evolving record of contribution." :description="intro.body" />
    <SectionWrapper>
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard v-for="metric in metrics" :key="metric.slug" :metric="metric" />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import StatCard from '../../components/cards/StatCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/impact');
const metrics = computed(() => payload.value?.data?.metrics || []);
const intro = computed(() => payload.value?.data?.intro?.[0] || {});
</script>
