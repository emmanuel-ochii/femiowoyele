<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Build Tomorrow"
      title="A platform for the builders coming next."
      description="A conference, a community, and a growing body of resources for people building companies, institutions, and public life across Africa and beyond."
      image="/images/engagement.jpg"
      image-alt="A conference room prepared for a Build Tomorrow session"
    >
      <template #actions>
        <BaseButton to="/contact" variant="gold" icon="arrowRight">Partner with Build Tomorrow</BaseButton>
      </template>
    </PageHero>

    <!-- ========================================================= sections -->
    <SectionWrapper>
      <SectionHeading
        eyebrow="The platform"
        title="Four ways it takes shape."
        lead="Build Tomorrow is deliberately more than an event. Each part feeds the others."
      />

      <div class="mt-14 grid gap-6 md:grid-cols-2">
        <ContentCard
          v-for="(section, index) in sections"
          :key="section.slug"
          v-reveal="index * 60"
          :to="sectionLink(section)"
          :title="section.title"
          :description="section.body"
          meta="Build Tomorrow"
          action="Read more"
        />
      </div>

      <EmptyState v-if="!sections.length" title="Build Tomorrow is being published." />
    </SectionWrapper>

    <!-- ============================================================ media -->
    <SectionWrapper v-if="media.length" tone="sand">
      <SectionHeading eyebrow="From the room" title="Talks and recordings" align="between">
        <template #action>
          <BaseButton to="/media" variant="outline" icon="arrowRight">Media hub</BaseButton>
        </template>
      </SectionHeading>

      <div :class="['mt-12 grid gap-6', cardGridClass(media.length)]">
        <MediaCard v-for="(item, index) in media" :key="item.slug" v-reveal="index * 60" :item="item" />
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="Get involved"
      title="Speak, sponsor, or bring your community."
      description="Partnership, programming, and participation enquiries for the conference and community."
      secondary-to="/mentorship"
      secondary-label="Mentorship"
      tone="white"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import MediaCard from '../../components/cards/MediaCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { cardGridClass } from '../../utils/layout';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/build-tomorrow');
const sections = computed(() => payload.value?.data?.sections || []);
const media = computed(() => payload.value?.data?.media || []);

const sectionLink = (section) => `/build-tomorrow/${section.slug.replace('build-tomorrow.', '')}`;

usePageMeta({
  title: 'Build Tomorrow',
  description:
    'Build Tomorrow — a conference, community, and knowledge platform for emerging builders shaping companies, institutions, and public life.',
});
</script>
