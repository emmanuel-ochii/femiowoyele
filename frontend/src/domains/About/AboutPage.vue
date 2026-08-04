<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="About"
      title="A life of work arranged around responsibility."
      description="Enterprise, governance, sustainability, mentorship, and authorship are not separate careers. They are one practice, approached from different rooms."
    >
      <template #actions>
        <BaseButton to="/work" variant="gold" icon="arrowRight">See the work</BaseButton>
        <BaseButton to="/contact" variant="outline-light">Start a conversation</BaseButton>
      </template>
    </PageHero>

    <!-- ======================================================== narrative -->
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">Who I am</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">The through-line</h2>
            <figure class="mt-10 hidden lg:block">
              <img
                src="/images/femi-portrait-sm.jpg"
                alt="Portrait of Femi Owoyele"
                class="aspect-[3/4] w-full max-w-[260px] object-cover object-top"
                loading="lazy"
                decoding="async"
              />
            </figure>
          </div>
        </div>

        <div class="lg:col-span-8">
          <article v-for="(block, index) in narrative" :key="block.slug" v-reveal="index * 70" class="border-t border-navy-900/12 pt-8 first:border-0 first:pt-0 [&+article]:mt-12">
            <p class="index-number">{{ String(index + 1).padStart(2, '0') }}</p>
            <h3 class="display-4 mt-4 text-navy-900">{{ block.title }}</h3>
            <p class="body-copy mt-5 max-w-2xl">{{ block.body }}</p>
          </article>

          <EmptyState v-if="!narrative.length" title="This narrative is being written." />
        </div>
      </div>
    </SectionWrapper>

    <!-- ====================================================== convictions -->
    <SectionWrapper tone="sand">
      <SectionHeading
        eyebrow="Guiding convictions"
        title="Four commitments that decide the work."
        lead="These are the tests every project, invitation, and commitment is measured against."
      />

      <div class="mt-14 grid [&>*]:-mb-px [&>*]:-mr-px sm:grid-cols-2">
        <div
          v-for="(conviction, index) in convictions"
          :key="conviction.id"
          v-reveal="index * 60"
          class="border border-navy-900/10 p-8 sm:p-10"
        >
          <p class="index-number">{{ String(conviction.order || index + 1).padStart(2, '0') }}</p>
          <h3 class="mt-6 font-serif text-2xl leading-snug text-navy-900">{{ conviction.title }}</h3>
          <p class="mt-4 text-[0.9rem] leading-7 text-ink-muted">{{ conviction.description }}</p>
        </div>
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="Next"
      title="The convictions become concrete in the work."
      description="Six pillars of practice, from enterprise and governance through to mentorship and public speaking."
      primary-to="/work"
      primary-label="Explore the pillars"
      secondary-to="/impact"
      secondary-label="See the record"
      tone="white"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
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

const { payload, loading, error, retry } = useApiPage('/about');
const narrative = computed(() => payload.value?.data?.narrative || []);
const convictions = computed(() => payload.value?.data?.convictions || []);

usePageMeta({
  title: 'About',
  description:
    'Femi Owoyele works across enterprise, leadership, governance, sustainability, mentorship, and authorship — one practice approached from different rooms.',
});
</script>
