<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Build Tomorrow"
      title="A platform for emerging builders."
      description="Conference, community, gallery, media, publications, partners, and future plans in one calm microsite structure."
      image="/images/femi-hero.png"
      image-alt="Lecture room prepared for a leadership engagement"
    />
    <SectionWrapper>
      <div class="grid gap-3 md:grid-cols-2">
        <ContentCard
          v-for="section in sections"
          :key="section.slug"
          :to="`/build-tomorrow/${section.slug.replace('build-tomorrow.', '')}`"
          :title="section.title"
          :description="section.body"
          meta="Build Tomorrow"
        />
      </div>
    </SectionWrapper>
    <SectionWrapper tone="sand">
      <p class="eyebrow">Gallery & Media</p>
      <div class="mt-8 grid gap-5 md:grid-cols-3">
        <ContentCard
          v-for="item in media"
          :key="item.slug"
          :title="item.title"
          :description="item.description"
          :meta="item.type"
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

const { payload, loading, error } = useApiPage('/build-tomorrow');
const sections = computed(() => payload.value?.data?.sections || []);
const media = computed(() => payload.value?.data?.media || []);
</script>
