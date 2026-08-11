<template>
  <LoadingState v-if="loading" label="Loading Entrusted" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="Entrusted could not be loaded."
    message="Something interrupted the connection. Please try again, or get in touch and we will send the details directly."
  />

  <div v-else>
    <!-- ============================================================ hero -->
    <section class="surface-navy on-dark relative overflow-hidden pt-header">
      <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute -left-48 top-0 h-[42rem] w-[42rem] rounded-full bg-gold-500/14 blur-[130px]"></div>
        <div class="absolute -right-40 bottom-0 h-[34rem] w-[34rem] rounded-full bg-gold-400/10 blur-[120px]"></div>
      </div>

      <div class="shell relative py-16 lg:py-24">
        <nav class="flex items-center gap-2 text-micro font-semibold uppercase text-white/40" aria-label="Breadcrumb">
          <RouterLink to="/books" class="transition-colors hover:text-gold-300">Books</RouterLink>
          <span aria-hidden="true">/</span>
          <span class="text-white/70">Entrusted</span>
        </nav>

        <div class="mt-12 grid gap-14 lg:grid-cols-12 lg:items-center lg:gap-16">
          <!-- The book -->
          <figure v-reveal class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
            <div class="relative isolate overflow-hidden border border-gold-500/45 px-3 py-4 shadow-frame sm:px-5 sm:py-6">
              <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-white via-sand-50 to-sand-200"></span>
              <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-sand-300/70 to-transparent"></span>
              <img
                :src="image"
                srcset="/images/entrusted-mock-sm.jpg 700w, /images/entrusted-mock.jpg 1280w"
                sizes="(min-width: 1024px) 36vw, (min-width: 640px) 24rem, 84vw"
                :alt="`Cover of ${title} by Femi Owoyele`"
                width="1280"
                height="1255"
                class="relative w-full"
                fetchpriority="high"
                decoding="async"
              />
            </div>

          </figure>

          <div class="lg:col-span-7">
            <p class="eyebrow !text-gold-400">Featured book</p>
            <h1 class="display-1 mt-6 text-balance text-white">{{ title }}</h1>
            <p v-if="subtitle" class="mt-5 font-serif text-xl italic text-gold-300 sm:text-2xl">{{ subtitle }}</p>
            <p v-if="occasion" class="mt-8 font-serif text-lg italic text-sand-300">{{ occasion }}</p>
            <p class="lead mt-6 max-w-2xl text-pretty !text-white/78">{{ body }}</p>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
              <BaseButton to="/pre-order" variant="gold" size="lg" icon="arrowRight">
                Pre-order Entrusted
              </BaseButton>
              <BaseButton :to="`/books/${bookSlug}`" variant="outline-light" size="lg">About the book</BaseButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======================================================= book meaning -->
    <SectionWrapper>
      <SectionHeading
        eyebrow="Why the book matters"
        title="A first book entering public conversation."
        lead="A clear invitation to think deeply about stewardship, responsibility, and building beyond ourselves."
      />

      <div class="mt-14 grid [&>*]:-mb-px [&>*]:-mr-px md:grid-cols-3">
        <div v-for="(item, index) in strands" :key="item.title" v-reveal="index * 70" class="border border-navy-900/10 p-8">
          <p class="index-number">{{ String(index + 1).padStart(2, '0') }}</p>
          <h3 class="mt-6 font-serif text-2xl leading-snug text-navy-900">{{ item.title }}</h3>
          <p class="mt-4 text-[0.9rem] leading-7 text-ink-muted">{{ item.description }}</p>
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================= about book -->
    <SectionWrapper v-if="book?.description">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">The book</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">What {{ title }} argues</h2>
            <BaseButton :to="`/books/${bookSlug}`" variant="ghost" class="mt-8" icon="arrowRight">
              Full book page
            </BaseButton>
          </div>
        </div>
        <div v-reveal="80" class="lg:col-span-8">
          <ProseBody :body="book.description" class="!max-w-none" />
        </div>
      </div>
    </SectionWrapper>

    <CtaBand
      eyebrow="The book"
      :title="`${title} is available for pre-order.`"
      description="Reserve your copy, read what the book argues, or get in touch about interviews, review copies, and bulk orders."
      primary-to="/pre-order"
      primary-label="Pre-order Entrusted"
      :secondary-to="`/books/${bookSlug}`"
      secondary-label="About the book"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import ProseBody from '../../components/sections/ProseBody.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/launch');

const event = computed(() => payload.value?.data?.event || {});
const book = computed(() => payload.value?.data?.book || null);
const meta = computed(() => event.value.meta || {});

const title = computed(() => event.value.title || 'Entrusted');
const subtitle = computed(() => meta.value.subtitle || book.value?.subtitle || '');
const occasion = computed(() => meta.value.occasion || '');
const image = computed(() => meta.value.image || '/images/entrusted-mock.jpg');
const bookSlug = computed(() => meta.value.book_slug || book.value?.slug || 'entrusted');
const body = computed(() => event.value.body || '');

const strands = [
  {
    title: 'A first book',
    description:
      'Entrusted gathers convictions formed through years of building, leading, teaching, mentoring, and public work.',
  },
  {
    title: 'A public invitation',
    description:
      'The book opens a wider conversation about stewardship, purpose, responsibility, and meaningful contribution.',
  },
  {
    title: 'A lasting lens',
    description:
      'A framework for holding time, gifts, opportunities, influence, resources, and life with greater intention.',
  },
];

usePageMeta(() => ({
  title: `${title.value} — Femi Owoyele`,
  description: `${title.value}: ${subtitle.value}. A book on stewardship, responsibility, and the making of a meaningful life.`,
}));
</script>
