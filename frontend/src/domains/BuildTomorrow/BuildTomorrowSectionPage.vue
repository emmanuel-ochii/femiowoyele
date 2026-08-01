<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero eyebrow="Build Tomorrow" :title="title" :description="description" />
    <SectionWrapper>
      <div class="grid gap-8">
        <article v-for="block in blocks" :key="block.slug" class="max-w-3xl">
          <h2 class="font-serif text-3xl text-navy">{{ block.title }}</h2>
          <p class="prose-content mt-4">{{ block.body }}</p>
        </article>
      </div>
    </SectionWrapper>
  </div>
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
const { payload, loading, error } = useApiPage(() => `/build-tomorrow/${route.params.section}`);
const blocks = computed(() => payload.value?.data || []);
const title = computed(() => blocks.value[0]?.title || 'Build Tomorrow');
const description = computed(() => blocks.value[0]?.body || '');
</script>
