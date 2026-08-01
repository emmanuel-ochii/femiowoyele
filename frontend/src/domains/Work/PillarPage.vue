<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero :eyebrow="'Work Pillar'" :title="pillar.title" :description="pillar.description" />
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">
        <div>
          <p class="eyebrow">{{ pillar.subtitle }}</p>
          <h2 class="heading-md mt-4 text-navy">Projects and related thinking</h2>
        </div>
        <div class="grid gap-10">
          <div>
            <h3 class="font-serif text-3xl text-navy">Projects</h3>
            <div class="mt-4 grid gap-2">
              <ContentCard
                v-for="project in pillar.projects"
                :key="project.slug"
                :title="project.title"
                :description="project.summary"
              />
            </div>
          </div>
          <div>
            <h3 class="font-serif text-3xl text-navy">Related Ideas</h3>
            <div class="mt-4 grid gap-2">
              <ContentCard
                v-for="article in pillar.articles"
                :key="article.slug"
                :to="`/research-ideas/${article.slug}`"
                :title="article.title"
                :description="article.excerpt"
                :meta="article.published_at"
              />
            </div>
          </div>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import ContentCard from '../../components/cards/ContentCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const route = useRoute();
const { payload, loading, error, load } = useApiPage(() => `/pillars/${route.params.pillarSlug}`);
watch(() => route.params.pillarSlug, load);
const pillar = computed(() => payload.value?.data || { projects: [], articles: [] });
</script>
