<template>
  <LoadingState v-if="loading" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="This entry could not be loaded."
    message="It may have moved, or the connection was interrupted. Try again, or browse the Journal."
  />

  <article v-else>
    <PageHero
      :breadcrumb="{ label: 'Journal', to: '/journal' }"
      :eyebrow="entry.category?.name || 'Journal'"
      :title="entry.title"
      :description="entry.excerpt"
    >
      <template #meta>
        <dl class="flex flex-wrap items-center gap-x-10 gap-y-3 text-micro font-semibold uppercase">
          <div class="flex items-center gap-2">
            <dt class="text-white/40">Published</dt>
            <dd class="text-white/75">{{ formatDate(entry.published_at) || '—' }}</dd>
          </div>
          <div v-if="minutes" class="flex items-center gap-2">
            <dt class="text-white/40">Length</dt>
            <dd class="text-white/75">{{ minutes }}</dd>
          </div>
        </dl>
      </template>
    </PageHero>

    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-8 lg:col-start-2">
          <ProseBody :body="entry.body" class="!max-w-none" />

          <div class="mt-16 border-t border-navy-900/12 pt-8">
            <p class="eyebrow">Continue</p>
            <div class="mt-6 flex flex-wrap gap-x-4 gap-y-3">
              <BaseButton to="/journal" variant="outline" icon="arrowRight">More entries</BaseButton>
              <BaseButton to="/research-ideas" variant="ghost">Long-form essays</BaseButton>
            </div>
          </div>
        </div>
      </div>
    </SectionWrapper>
  </article>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import PageHero from '../../components/sections/PageHero.vue';
import ProseBody from '../../components/sections/ProseBody.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate, readingTime } from '../../utils/format';

const route = useRoute();
const { payload, loading, error, load, retry } = useApiPage(() => `/journal/${route.params.slug}`);

watch(() => route.params.slug, load);

const entry = computed(() => payload.value?.data || {});
const minutes = computed(() => readingTime(entry.value.body));

usePageMeta(() => ({
  title: entry.value.title || 'Journal',
  description: entry.value.excerpt,
}));
</script>
