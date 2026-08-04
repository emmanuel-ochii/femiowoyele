<template>
  <div>
    <PageHero
      eyebrow="Media"
      title="Interviews, talks, and public record."
      description="A restrained archive of conversations, broadcasts, and images from the work — for journalists, organisers, and anyone who wants the primary source."
    />

    <SectionWrapper tone="paper">
      <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <FilterBar v-model="selectedType" :options="types" label="Filter media by type" class="flex-1" />
        <p class="shrink-0 text-micro font-semibold uppercase text-ink-faint sm:pb-5">
          {{ items.length }} {{ items.length === 1 ? 'item' : 'items' }}
        </p>
      </div>

      <LoadingState v-if="loading" class="!px-0" />
      <ErrorState v-else-if="error" :on-retry="retry" class="!px-0" />

      <div v-else-if="items.length" :class="['mt-12 grid gap-6', cardGridClass(items.length)]">
        <MediaCard v-for="(item, index) in items" :key="item.slug" v-reveal="index * 50" :item="item" />
      </div>

      <EmptyState
        v-else
        class="mt-12"
        title="Nothing filed under this type yet."
        message="Try another category — interviews, broadcasts, and recordings are added as they are published."
      />
    </SectionWrapper>

    <CtaBand
      eyebrow="Press"
      title="Working on a story or a booking?"
      description="Interview requests, biography and photography, and speaking availability."
    />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import MediaCard from '../../components/cards/MediaCard.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import FilterBar from '../../components/ui/FilterBar.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { cardGridClass } from '../../utils/layout';

const types = [
  { label: 'All', value: '' },
  { label: 'Interviews', value: 'interview' },
  { label: 'Television', value: 'tv' },
  { label: 'Podcasts', value: 'podcast' },
  { label: 'Video', value: 'video' },
  { label: 'Gallery', value: 'image' },
  { label: 'Downloads', value: 'download' },
];

const selectedType = ref('');
const { payload, loading, error, load, retry } = useApiPage('/media', () =>
  selectedType.value ? { type: selectedType.value } : {},
);

watch(selectedType, () => load());

const items = computed(() => payload.value?.data || []);

usePageMeta({
  title: 'Media',
  description: 'Interviews, broadcasts, podcasts, talks, and images from the work of Femi Owoyele.',
});
</script>
