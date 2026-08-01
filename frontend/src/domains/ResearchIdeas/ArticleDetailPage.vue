<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <article v-else>
    <PageHero :eyebrow="article.category?.name || 'Research & Ideas'" :title="article.title" :description="article.excerpt" />
    <SectionWrapper>
      <div class="prose prose-lg max-w-3xl prose-headings:font-serif prose-headings:text-navy prose-p:text-ink/78">
        <p>{{ article.body }}</p>
      </div>
    </SectionWrapper>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const route = useRoute();
const { payload, loading, error } = useApiPage(() => `/research-ideas/${route.params.slug}`);
const article = computed(() => payload.value?.data || {});
</script>
