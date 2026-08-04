<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Speaking"
      title="Serious conversations for serious rooms."
      description="Keynotes, lectures, panels, and private leadership sessions on enterprise, governance, stewardship, and the formation of the next generation of builders."
      image="/images/engagement.jpg"
      image-alt="A lectern prepared ahead of a leadership address"
    >
      <template #actions>
        <BaseButton href="#enquiry" variant="gold" icon="arrowRight">Send an invitation</BaseButton>
      </template>
    </PageHero>

    <!-- =========================================================== topics -->
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">What I speak about</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">Themes and formats</h2>
            <p class="body-copy mt-5">
              Every session is shaped around the room it is written for — a board, a lecture hall, a founder cohort, or
              a public audience.
            </p>
          </div>
        </div>

        <div class="lg:col-span-8">
          <article
            v-for="(block, index) in content"
            :key="block.slug"
            v-reveal="index * 70"
            class="border-t border-navy-900/12 pt-8 first:border-0 first:pt-0 [&+article]:mt-12"
          >
            <h3 class="display-4 text-navy-900">{{ block.title }}</h3>
            <p class="body-copy mt-5 max-w-2xl">{{ block.body }}</p>

            <ul v-if="block.meta?.audiences?.length" class="mt-7 flex flex-wrap gap-2">
              <li
                v-for="audience in block.meta.audiences"
                :key="audience"
                class="border border-navy-900/12 bg-sand-50 px-3.5 py-1.5 text-[0.78rem] font-medium text-navy-800 first-letter:uppercase"
              >
                {{ audience }}
              </li>
            </ul>
          </article>

          <EmptyState v-if="!content.length" title="Speaking topics are being published." />
        </div>
      </div>
    </SectionWrapper>

    <!-- ============================================================ media -->
    <SectionWrapper v-if="media.length" tone="sand">
      <SectionHeading
        eyebrow="On the record"
        title="Recent talks and conversations"
        align="between"
      >
        <template #action>
          <BaseButton to="/media" variant="outline" icon="arrowRight">Media hub</BaseButton>
        </template>
      </SectionHeading>

      <div :class="['mt-12 grid gap-6', cardGridClass(media.length)]">
        <MediaCard v-for="(item, index) in media" :key="item.slug" v-reveal="index * 60" :item="item" />
      </div>
    </SectionWrapper>

    <!-- ========================================================= enquiry -->
    <SectionWrapper id="enquiry" tone="paper">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-5">
          <p class="eyebrow">Speaking enquiry</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">Tell me about the room.</h2>
          <p class="body-copy mt-5">
            The most useful invitations describe the audience, the moment the organisation is in, and what should be
            different once the session ends.
          </p>

          <ul class="mt-10 space-y-5">
            <li v-for="item in enquiryChecklist" :key="item" class="flex gap-3.5">
              <AppIcon name="check" :size="16" class="mt-1 shrink-0 text-gold-600" />
              <span class="text-[0.9rem] leading-7 text-ink-muted">{{ item }}</span>
            </li>
          </ul>
        </div>

        <div v-reveal="80" class="lg:col-span-7">
          <div class="border border-navy-900/10 bg-white p-7 shadow-soft sm:p-10">
            <ContactForm
              default-type="speaking"
              subject-placeholder="Event name and date"
              message-placeholder="Audience, format, dates, location, and what a successful session looks like."
            />
          </div>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import MediaCard from '../../components/cards/MediaCard.vue';
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
import { cardGridClass } from '../../utils/layout';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/speaking');
const content = computed(() => payload.value?.data?.content || []);
const media = computed(() => payload.value?.data?.media || []);

const enquiryChecklist = [
  'Who is in the room, and how many of them.',
  'The date, city, and format — keynote, lecture, panel, or closed session.',
  'The decision or shift the session is meant to support.',
  'Whether recording, press, or a written follow-up is expected.',
];

usePageMeta({
  title: 'Speaking',
  description:
    'Keynotes, lectures, panels, and private leadership sessions on enterprise, governance, stewardship, and mentorship.',
});
</script>
