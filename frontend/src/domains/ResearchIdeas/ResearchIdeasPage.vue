<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Research & Ideas"
      title="Essays and frameworks for builders, leaders, and institutions."
      description="Articles are grouped by category and designed for long-term readability."
    />
    <SectionWrapper>
      <div class="mb-8 flex flex-wrap gap-3">
        <button
          class="focus-ring border px-4 py-2 text-sm font-semibold"
          :class="!category ? 'border-forest bg-forest text-white' : 'border-navy/15 text-navy'"
          type="button"
          @click="setCategory('')"
        >
          All
        </button>
        <button
          v-for="item in categories"
          :key="item.slug"
          class="focus-ring border px-4 py-2 text-sm font-semibold"
          :class="category === item.slug ? 'border-forest bg-forest text-white' : 'border-navy/15 text-navy'"
          type="button"
          @click="setCategory(item.slug)"
        >
          {{ item.name }}
        </button>
      </div>
      <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
        <ContentCard
          v-for="article in articles"
          :key="article.slug"
          :to="`/research-ideas/${article.slug}`"
          :title="article.title"
          :description="article.excerpt"
          :meta="article.category?.name"
        />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { contentApi } from '../../services/contentApi';
import { useApiPage } from '../../composables/useApiPage';

const category = ref('');
const { payload, loading, error } = useApiPage('/research-ideas');
const articles = computed(() => payload.value?.data || []);
const categories = computed(() => payload.value?.meta?.categories || []);

const setCategory = async (slug) => {
  category.value = slug;
  payload.value = await contentApi.get('/research-ideas', slug ? { category: slug } : {});
};
</script>
