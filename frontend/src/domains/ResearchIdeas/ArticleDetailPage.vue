<template>
  <LoadingState v-if="loading" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="This essay could not be loaded."
    message="It may have moved, or the connection was interrupted. Try again, or browse all essays."
  />

  <article v-else>
    <PageHero
      :breadcrumb="{ label: 'Research & Ideas', to: '/research-ideas' }"
      :eyebrow="article.category?.name || 'Essay'"
      :title="article.title"
      :description="article.excerpt"
    >
      <template #meta>
        <dl class="flex flex-wrap items-center gap-x-10 gap-y-3 text-micro font-semibold uppercase">
          <div class="flex items-center gap-2">
            <dt class="text-white/40">Published</dt>
            <dd class="text-white/75">{{ formatDate(article.published_at) || '—' }}</dd>
          </div>
          <div v-if="minutes" class="flex items-center gap-2">
            <dt class="text-white/40">Length</dt>
            <dd class="text-white/75">{{ minutes }}</dd>
          </div>
          <div v-if="article.pillar?.title" class="flex items-center gap-2">
            <dt class="text-white/40">Pillar</dt>
            <dd class="text-white/75">{{ article.pillar.title }}</dd>
          </div>
        </dl>
      </template>
    </PageHero>

    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-8 lg:col-start-2">
          <ProseBody :body="article.body" class="!max-w-none" />

          <div class="mt-16 border-t border-navy-900/12 pt-8">
            <p class="eyebrow">Continue</p>
            <div class="mt-6 flex flex-wrap gap-x-4 gap-y-3">
              <BaseButton to="/research-ideas" variant="outline" icon="arrowRight">More essays</BaseButton>
              <BaseButton v-if="article.pillar?.slug" :to="`/work/${article.pillar.slug}`" variant="ghost">
                About {{ article.pillar.title }}
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="Stay close to the work"
      title="New essays, sent occasionally."
      description="Long-form writing on enterprise, governance, and the work of building institutions worth trusting."
      primary-to="/contact"
      primary-label="Get in touch"
      secondary-to="/journal"
      secondary-label="Read the Journal"
    />
  </article>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import CtaBand from '../../components/sections/CtaBand.vue';
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
const { payload, loading, error, load, retry } = useApiPage(() => `/research-ideas/${route.params.slug}`);

watch(() => route.params.slug, load);

const article = computed(() => payload.value?.data || {});
const minutes = computed(() => readingTime(article.value.body));

usePageMeta(() => ({
  title: article.value.title || 'Essay',
  description: article.value.excerpt,
}));
</script>
