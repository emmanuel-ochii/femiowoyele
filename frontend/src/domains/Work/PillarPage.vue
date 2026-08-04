<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      :eyebrow="pillar.title"
      :breadcrumb="{ label: 'My Work', to: '/work' }"
      :title="pillar.subtitle || pillar.title"
      :description="pillar.description"
    >
      <template #actions>
        <BaseButton to="/contact" variant="gold" icon="arrowRight">Discuss this work</BaseButton>
        <BaseButton to="/work" variant="outline-light">All pillars</BaseButton>
      </template>
    </PageHero>

    <!-- ========================================================= projects -->
    <SectionWrapper v-if="projects.length">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">In practice</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">Projects and initiatives</h2>
            <p class="body-copy mt-5">Where this pillar has taken concrete shape.</p>
          </div>
        </div>

        <div class="lg:col-span-8">
          <div class="border-b border-navy-900/12">
            <ContentCard
              v-for="(project, index) in projects"
              :key="project.slug"
              v-reveal="index * 60"
              variant="list"
              :title="project.title"
              :description="project.summary"
              meta="Project"
            />
          </div>
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================= articles -->
    <SectionWrapper v-if="articles.length" tone="sand">
      <SectionHeading
        eyebrow="Related thinking"
        title="Essays from this pillar"
        align="between"
      >
        <template #action>
          <BaseButton to="/research-ideas" variant="outline" icon="arrowRight">All essays</BaseButton>
        </template>
      </SectionHeading>

      <div :class="['mt-12 grid gap-6', cardGridClass(articles.length)]">
        <ContentCard
          v-for="(article, index) in articles"
          :key="article.slug"
          v-reveal="index * 60"
          :to="`/research-ideas/${article.slug}`"
          :title="article.title"
          :description="article.excerpt"
          :meta="article.category?.name"
          :date="formatDate(article.published_at)"
          action="Read the essay"
        />
      </div>
    </SectionWrapper>

    <EmptyState
      v-if="!projects.length && !articles.length"
      class="mx-auto my-20 max-w-3xl"
      title="This pillar is being documented."
      message="Projects, essays, and field notes for this area of work are added as they are published."
    />

    <CtaBand
      eyebrow="Get in touch"
      :title="`Working on something in ${pillar.title?.toLowerCase() || 'this area'}?`"
      description="Advisory, speaking, collaboration, and mentorship enquiries are all welcome."
    />
  </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import ContentCard from '../../components/cards/ContentCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate } from '../../utils/format';
import { cardGridClass } from '../../utils/layout';

const route = useRoute();
const { payload, loading, error, load, retry } = useApiPage(() => `/pillars/${route.params.pillarSlug}`);

watch(() => route.params.pillarSlug, load);

const pillar = computed(() => payload.value?.data || {});
const projects = computed(() => pillar.value.projects || []);
const articles = computed(() => pillar.value.articles || []);

usePageMeta(() => ({
  title: pillar.value.title || 'Work',
  description: pillar.value.description,
}));
</script>
