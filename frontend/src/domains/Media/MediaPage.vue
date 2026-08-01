<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Media"
      title="Interviews, video, gallery, and public record."
      description="A restrained media hub for interviews, TV, podcasts, videos, images, and downloads."
    />
    <SectionWrapper tone="sand">
      <div class="mb-8 flex flex-wrap gap-3">
        <button
          v-for="type in types"
          :key="type.value"
          class="focus-ring border px-4 py-2 text-sm font-semibold"
          :class="selectedType === type.value ? 'border-forest bg-forest text-white' : 'border-navy/15 bg-white text-navy'"
          type="button"
          @click="selectType(type.value)"
        >
          {{ type.label }}
        </button>
      </div>
      <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <ContentCard
          v-for="item in items"
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
import { computed, ref } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { contentApi } from '../../services/contentApi';

const types = [
  { label: 'All', value: '' },
  { label: 'Interviews', value: 'interview' },
  { label: 'TV', value: 'tv' },
  { label: 'Podcasts', value: 'podcast' },
  { label: 'Videos', value: 'video' },
  { label: 'Gallery', value: 'image' },
  { label: 'Downloads', value: 'download' },
];

const selectedType = ref('');
const { payload, loading, error } = useApiPage('/media');
const items = computed(() => payload.value?.data || []);

const selectType = async (type) => {
  selectedType.value = type;
  payload.value = await contentApi.get('/media', type ? { type } : {});
};
</script>
