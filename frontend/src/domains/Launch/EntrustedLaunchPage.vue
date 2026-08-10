<template>
  <LoadingState v-if="loading" label="Loading the launch" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="The launch details could not be loaded."
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
          <span class="text-white/70">The launch</span>
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

          <!-- The event -->
          <div class="lg:col-span-7">
            <p class="eyebrow !text-gold-400">{{ hasPassed ? 'Published' : 'The unveiling' }}</p>
            <h1 class="display-1 mt-6 text-balance text-white">{{ title }}</h1>
            <p v-if="subtitle" class="mt-5 font-serif text-xl italic text-gold-300 sm:text-2xl">{{ subtitle }}</p>
            <p v-if="occasion" class="mt-8 font-serif text-lg italic text-sand-300">{{ occasion }}</p>
            <p class="lead mt-6 max-w-2xl text-pretty !text-white/78">{{ body }}</p>

            <div class="mt-10">
              <p class="text-micro font-semibold uppercase text-gold-400">
                {{ hasPassed ? 'Unveiled' : isToday ? 'Today' : 'Counting down' }}
              </p>
              <div v-if="!hasPassed" class="mt-5 grid max-w-lg grid-cols-3 divide-x divide-white/15 border-y border-white/15">
                <div v-for="unit in countdownUnits" :key="unit.label" class="px-5 py-6 first:pl-0">
                  <p class="font-serif text-4xl leading-none tabular-nums text-white sm:text-5xl">{{ unit.value }}</p>
                  <p class="mt-3 text-micro font-semibold uppercase text-white/50">{{ unit.label }}</p>
                </div>
              </div>
              <p v-else class="mt-4 font-serif text-2xl text-white">Launched on {{ dateLabel }} in Lagos.</p>
            </div>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
              <BaseButton to="/pre-order" variant="gold" size="lg" icon="arrowRight">
                {{ hasPassed ? 'Reserve a copy' : 'Pre-order Entrusted' }}
              </BaseButton>
              <BaseButton :to="`/books/${bookSlug}`" variant="outline-light" size="lg">About the book</BaseButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== launch meaning -->
    <SectionWrapper>
      <SectionHeading
        eyebrow="Why the launch matters"
        title="A first book entering public conversation."
        lead="An evening shaped around stewardship, responsibility, and the invitation to build beyond ourselves."
      />

      <div class="mt-14 grid [&>*]:-mb-px [&>*]:-mr-px md:grid-cols-3">
        <div v-for="(item, index) in strands" :key="item.title" v-reveal="index * 70" class="border border-navy-900/10 p-8">
          <p class="index-number">{{ String(index + 1).padStart(2, '0') }}</p>
          <h3 class="mt-6 font-serif text-2xl leading-snug text-navy-900">{{ item.title }}</h3>
          <p class="mt-4 text-[0.9rem] leading-7 text-ink-muted">{{ item.description }}</p>
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================= the evening -->
    <SectionWrapper tone="sand">
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-5">
          <p class="eyebrow">The evening</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">Where and when</h2>
          <p class="body-copy mt-5">
            An evening of gratitude and great beginnings, with family, friends, mentors, and the builders who have shaped
            the work — closing with the first public reading from {{ title }}.
          </p>
        </div>

        <div v-reveal="80" class="lg:col-span-7">
          <dl class="grid gap-x-10 gap-y-8 border-t border-navy-900/12 pt-8 sm:grid-cols-2">
            <div v-for="detail in details" :key="detail.label">
              <dt class="text-micro font-semibold uppercase text-forest-700">{{ detail.label }}</dt>
              <dd class="mt-2.5 font-serif text-xl text-navy-900">{{ detail.value }}</dd>
            </div>
          </dl>

          <ul class="mt-10 space-y-5">
            <li v-for="item in runOfShow" :key="item" class="flex gap-3.5">
              <AppIcon name="check" :size="16" class="mt-1 shrink-0 text-gold-600" />
              <span class="text-[0.9rem] leading-7 text-ink-muted">{{ item }}</span>
            </li>
          </ul>
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
      :title="hasPassed ? `${title} is now in public conversation.` : `${title} is available for pre-order.`"
      description="Reserve your copy, read what the book argues, or get in touch about interviews, review copies, and bulk orders."
      primary-to="/pre-order"
      :primary-label="hasPassed ? 'Reserve a copy' : 'Pre-order Entrusted'"
      :secondary-to="`/books/${bookSlug}`"
      secondary-label="About the book"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import ProseBody from '../../components/sections/ProseBody.vue';
import AppIcon from '../../components/ui/AppIcon.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { useCountdown } from '../../composables/useCountdown';
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
const dateLabel = computed(() => meta.value.date_label || '');

const { parts, hasPassed, isToday } = useCountdown(() => meta.value.starts_at);

const body = computed(() =>
  hasPassed.value && meta.value.body_after ? meta.value.body_after : event.value.body || '',
);

const countdownUnits = computed(() => [
  { label: parts.value.days === 1 ? 'Day' : 'Days', value: parts.value.days },
  { label: parts.value.hours === 1 ? 'Hour' : 'Hours', value: parts.value.hours },
  { label: parts.value.minutes === 1 ? 'Minute' : 'Minutes', value: parts.value.minutes },
]);

const details = computed(() =>
  [
    { label: 'Date', value: dateLabel.value },
    { label: 'Time', value: meta.value.time_label },
    { label: 'Venue', value: meta.value.venue },
    { label: 'Address', value: meta.value.address },
  ].filter((detail) => Boolean(detail.value)),
);

const strands = [
  {
    title: 'A first book',
    description:
      'Entrusted gathers convictions formed through years of building, leading, teaching, mentoring, and public work.',
  },
  {
    title: 'A public invitation',
    description:
      'The launch opens a wider conversation about stewardship, purpose, responsibility, and meaningful contribution.',
  },
  {
    title: 'One evening',
    description:
      'Gratitude for what has shaped the work, and a beginning for what the book is meant to start in other people.',
  },
];

const runOfShow = [
  'Arrival and welcome from 4:00 p.m.',
  'Reflections from family, friends, mentors, and builders connected to the work.',
  'The unveiling of Entrusted, and a first reading from the book.',
  'Dinner, and time to greet the author.',
];

usePageMeta(() => ({
  title: `${title.value} — the launch`,
  description: `${title.value}: ${subtitle.value}. Unveiled on ${dateLabel.value} at ${meta.value.venue || 'Lagos'}.`,
}));
</script>
