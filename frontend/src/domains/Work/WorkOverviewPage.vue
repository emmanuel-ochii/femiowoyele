<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="My work"
      title="Six pillars, one practice."
      description="Enterprise, leadership, governance, sustainability, mentorship, and speaking. Each is a discipline in its own right; together they describe how the work is done."
    />

    <SectionWrapper tone="paper">
      <div class="grid [&>*]:-mb-px [&>*]:-mr-px sm:grid-cols-2 lg:grid-cols-3">
        <PillarCard v-for="(pillar, index) in pillars" :key="pillar.slug" v-reveal="index * 60" :pillar="pillar" />
      </div>

      <EmptyState v-if="!pillars.length" title="The pillars are being prepared." />
    </SectionWrapper>

    <CtaBand
      eyebrow="Work together"
      title="Bring this practice into your room."
      description="Board advisory, keynote engagements, mentorship cohorts, and research collaborations."
      primary-to="/contact"
      primary-label="Start a conversation"
      secondary-to="/speaking"
      secondary-label="Speaking"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import PillarCard from '../../components/cards/PillarCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/pillars');
const pillars = computed(() => payload.value?.data || []);

usePageMeta({
  title: 'My Work',
  description:
    'Six pillars of practice: enterprise, leadership, governance, sustainability, mentorship, and speaking.',
});
</script>
