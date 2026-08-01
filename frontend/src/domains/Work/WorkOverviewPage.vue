<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="My Work"
      title="Enterprise, leadership, governance, sustainability, mentorship, and speaking."
      description="A structured overview of the content pillars defined in the specification."
    />
    <SectionWrapper tone="sand">
      <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <PillarCard v-for="pillar in pillars" :key="pillar.slug" :pillar="pillar" />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import PillarCard from '../../components/cards/PillarCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/pillars');
const pillars = computed(() => payload.value?.data || []);
</script>
