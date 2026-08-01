<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="About"
      title="A life of work arranged around responsibility."
      description="This page implements the identity narrative and guiding convictions described in the specification."
    />
    <SectionWrapper>
      <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr]">
        <div>
          <p class="eyebrow">Narrative</p>
          <h2 class="heading-md mt-4 text-navy">Who I Am</h2>
        </div>
        <div class="grid gap-8">
          <article v-for="block in data.narrative" :key="block.slug">
            <h3 class="font-serif text-3xl text-navy">{{ block.title }}</h3>
            <p class="prose-content mt-4">{{ block.body }}</p>
          </article>
        </div>
      </div>
    </SectionWrapper>
    <SectionWrapper tone="sand">
      <p class="eyebrow">Guiding Convictions</p>
      <div class="mt-8 grid gap-5 md:grid-cols-2">
        <ContentCard
          v-for="conviction in data.convictions"
          :key="conviction.id"
          :title="conviction.title"
          :description="conviction.description"
          :meta="String(conviction.order).padStart(2, '0')"
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

const { payload, loading, error } = useApiPage('/about');
const data = computed(() => payload.value?.data || { narrative: [], convictions: [] });
</script>
