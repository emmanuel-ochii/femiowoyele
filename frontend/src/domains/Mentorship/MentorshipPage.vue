<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Mentorship"
      title="Building Builders."
      description="Mentorship here is treated as formation, not motivation: judgment, discipline, context, and the courage to carry weight that lasts."
    >
      <template #actions>
        <BaseButton href="#apply" variant="gold" icon="arrowRight">Apply for mentorship</BaseButton>
      </template>
    </PageHero>

    <!-- ====================================================== philosophy -->
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">The approach</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">What mentorship is for</h2>
          </div>
        </div>

        <div class="lg:col-span-8">
          <article
            v-for="(block, index) in content"
            :key="block.slug"
            v-reveal="index * 70"
            class="border-t border-navy-900/12 pt-8 first:border-0 first:pt-0 [&+article]:mt-12"
          >
            <p class="index-number">{{ String(index + 1).padStart(2, '0') }}</p>
            <h3 class="display-4 mt-4 text-navy-900">{{ block.title }}</h3>
            <p class="body-copy mt-5 max-w-2xl">{{ block.body }}</p>
          </article>

          <EmptyState v-if="!content.length" title="The mentorship programme is being published." />
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================== who for -->
    <SectionWrapper tone="navy">
      <SectionHeading
        dark
        eyebrow="Who this is for"
        title="Builders who are already carrying something."
        lead="Mentorship works best when there is real work on the table — a company, a team, an institution, or a decision that will not wait."
      />

      <!-- Dividers rather than a gap-fill, so the section's gradient stays unbroken. -->
      <div class="mt-14 grid divide-y divide-white/12 border-y border-white/12 lg:grid-cols-3 lg:divide-x lg:divide-y-0">
        <div v-for="(item, index) in audiences" :key="item.title" v-reveal="index * 60" class="p-8 lg:first:pl-0">
          <p class="index-number">{{ String(index + 1).padStart(2, '0') }}</p>
          <h3 class="mt-6 font-serif text-xl text-white">{{ item.title }}</h3>
          <p class="mt-3 text-[0.9rem] leading-7 text-white/60">{{ item.description }}</p>
        </div>
      </div>
    </SectionWrapper>

    <!-- ============================================================ apply -->
    <SectionWrapper id="apply" tone="paper">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-5">
          <p class="eyebrow">Applications</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">Make the case for your work.</h2>
          <p class="body-copy mt-5">
            Cohorts are small and applications are read personally. Write plainly about what you are building, what is
            currently hard, and what you want to be true a year from now.
          </p>

          <ul class="mt-10 space-y-5">
            <li v-for="item in applicationChecklist" :key="item" class="flex gap-3.5">
              <AppIcon name="check" :size="16" class="mt-1 shrink-0 text-gold-600" />
              <span class="text-[0.9rem] leading-7 text-ink-muted">{{ item }}</span>
            </li>
          </ul>
        </div>

        <div v-reveal="80" class="lg:col-span-7">
          <div class="border border-navy-900/10 bg-white p-7 shadow-soft sm:p-10">
            <ContactForm
              default-type="mentorship"
              subject-placeholder="What you are building"
              message-placeholder="Your work so far, the decision or constraint in front of you, and what support would change."
            />
          </div>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ContactForm from '../../components/forms/ContactForm.vue';
import PageHero from '../../components/sections/PageHero.vue';
import AppIcon from '../../components/ui/AppIcon.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/mentorship');
const content = computed(() => payload.value?.data?.content || []);

const audiences = [
  {
    title: 'Founders and operators',
    description: 'Building companies past the point where personal effort alone can hold them together.',
  },
  {
    title: 'Emerging leaders',
    description: 'Taking on responsibility ahead of experience, and looking for judgment rather than tactics.',
  },
  {
    title: 'Institution builders',
    description: 'Working inside boards, public bodies, and civic organisations that must earn public confidence.',
  },
];

const applicationChecklist = [
  'What you are building, in plain language.',
  'The decision, constraint, or tension you are currently sitting with.',
  'What you have already tried, and what you learned from it.',
  'What you would like to be true in twelve months.',
];

usePageMeta({
  title: 'Mentorship',
  description:
    'Building Builders — mentorship as formation for founders, emerging leaders, and institution builders.',
});
</script>
