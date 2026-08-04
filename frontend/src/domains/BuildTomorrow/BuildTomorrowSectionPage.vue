<template>
  <LoadingState v-if="loading" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="This section could not be loaded."
    message="It may have moved, or the connection was interrupted. Try again, or return to the Build Tomorrow overview."
  />

  <div v-else>
    <PageHero
      :breadcrumb="{ label: 'Build Tomorrow', to: '/build-tomorrow' }"
      :eyebrow="lead.title || 'Section'"
      :title="lead.title || 'Build Tomorrow'"
      :description="lead.body"
    />

    <SectionWrapper v-if="rest.length">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-8 lg:col-start-2">
          <article v-for="(block, index) in rest" :key="block.slug" v-reveal="index * 70" class="[&+article]:mt-14">
            <h2 class="display-4 text-navy-900">{{ block.title }}</h2>
            <ProseBody :body="block.body" class="mt-5 !max-w-none" />
          </article>
        </div>
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="Build Tomorrow"
      title="Bring your community into the conversation."
      description="Partnership, speaking, and participation enquiries."
      secondary-to="/build-tomorrow"
      secondary-label="Back to overview"
    />
  </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ProseBody from '../../components/sections/ProseBody.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const route = useRoute();
const { payload, loading, error, load, retry } = useApiPage(() => `/build-tomorrow/${route.params.section}`);

watch(() => route.params.section, load);

const blocks = computed(() => payload.value?.data || []);
const lead = computed(() => blocks.value[0] || {});
const rest = computed(() => blocks.value.slice(1));

usePageMeta(() => ({
  title: lead.value.title ? `${lead.value.title} — Build Tomorrow` : 'Build Tomorrow',
  description: lead.value.body,
}));
</script>
