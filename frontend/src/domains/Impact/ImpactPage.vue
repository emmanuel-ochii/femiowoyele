<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Impact"
      title="An evolving record, not a trophy case."
      :description="intro.body || 'Numbers are only ever part of the picture. Read together, they mark the shape of a body of work still being built.'"
    />

    <SectionWrapper>
      <div class="grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard v-for="(metric, index) in metrics" :key="metric.slug" v-reveal="index * 60" :metric="metric" />
      </div>

      <EmptyState v-if="!metrics.length" title="Impact figures are being compiled." />
    </SectionWrapper>

    <SectionWrapper tone="sand">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-5">
          <p class="eyebrow">How to read this</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">What the figures do and do not say.</h2>
        </div>
        <div v-reveal="80" class="lg:col-span-7">
          <p class="lead text-pretty">
            Counting people mentored or rooms addressed says something real about reach. It says far less about
            formation — whether judgment improved, whether an institution became more trustworthy, whether a builder
            stayed the course when it was expensive to do so.
          </p>
          <p class="body-copy mt-6">
            That work is slower and harder to measure, so this page is published with a deliberate caution: treat these
            figures as a signal of activity, and the essays, books, and mentorship rooms as the evidence of intent.
          </p>
          <BaseButton to="/about" variant="ghost" class="mt-8" icon="arrowRight">Read the convictions behind it</BaseButton>
        </div>
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="Add to the record"
      title="Partner on work that compounds."
      description="Advisory, mentorship cohorts, research collaborations, and public engagements."
      secondary-to="/work"
      secondary-label="See the pillars"
      tone="white"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import StatCard from '../../components/cards/StatCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/impact');
const metrics = computed(() => payload.value?.data?.metrics || []);
const intro = computed(() => payload.value?.data?.intro?.[0] || {});

usePageMeta({
  title: 'Impact',
  description: 'An evolving record of contribution across mentorship, public engagement, and community work.',
});
</script>
