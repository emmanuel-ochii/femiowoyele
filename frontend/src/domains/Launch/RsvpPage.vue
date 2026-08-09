<template>
  <section class="surface-navy on-dark relative min-h-screen overflow-hidden pt-header">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -left-48 top-0 h-[40rem] w-[40rem] rounded-full bg-gold-500/12 blur-[130px]"></div>
      <div class="absolute -right-40 bottom-0 h-[32rem] w-[32rem] rounded-full bg-gold-400/10 blur-[120px]"></div>
    </div>

    <div class="shell relative py-14 lg:py-20">
      <LoadingState v-if="loading" label="Loading the invitation" class="!px-0" />
      <ErrorState
        v-else-if="error"
        :on-retry="retry"
        title="The invitation could not be loaded."
        message="Something interrupted the connection. Please try again in a moment."
      />

      <div v-else class="grid gap-12 lg:grid-cols-12 lg:items-start lg:gap-16">
        <!-- ----------------------------------------------------- the invite -->
        <div class="lg:col-span-5">
          <p class="eyebrow !text-gold-400">You are invited</p>
          <h1 class="display-2 mt-6 text-balance text-white">{{ heading }}</h1>
          <p v-if="occasion" class="mt-5 font-serif text-lg italic text-sand-300">{{ occasion }}</p>
          <p class="mt-6 max-w-md text-[0.95rem] leading-7 text-white/65">{{ intro }}</p>

          <dl class="mt-10 grid gap-x-8 gap-y-6 border-t border-white/15 pt-8 sm:grid-cols-2">
            <div v-for="detail in details" :key="detail.label">
              <dt class="text-micro font-semibold uppercase text-gold-400">{{ detail.label }}</dt>
              <dd class="mt-2 text-[0.95rem] leading-6 text-white/80">{{ detail.value }}</dd>
            </div>
          </dl>

          <div v-if="!hasPassed && countdownLabel" class="mt-8">
            <span class="inline-flex items-center gap-3 border border-white/15 px-4 py-3">
              <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>
              <span class="text-micro font-semibold uppercase text-white/70">{{ countdownLabel }}</span>
            </span>
          </div>

          <div class="mt-8">
            <BaseButton to="/entrusted" variant="ghost-light" size="sm" icon="arrowRight">
              Read about the launch
            </BaseButton>
          </div>
        </div>

        <!-- ------------------------------------------------------- the form -->
        <div class="lg:col-span-7">
          <div class="on-light border border-navy-900/10 bg-white p-7 shadow-frame sm:p-10">
            <div v-if="hasPassed" class="text-center">
              <h2 class="display-4 text-navy-900">This evening has passed.</h2>
              <p class="body-copy mx-auto mt-4 max-w-md">
                Thank you to everyone who joined us. {{ title }} is now published.
              </p>
              <BaseButton :to="`/books/${bookSlug}`" variant="primary" class="mt-8" icon="arrowRight">
                About the book
              </BaseButton>
            </div>

            <div v-else-if="rsvpClosed" class="text-center">
              <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-navy-900/15 text-ink-faint">
                <AppIcon name="mail" :size="22" />
              </span>
              <h2 class="display-4 mt-6 text-navy-900">RSVPs have closed.</h2>
              <p class="body-copy mx-auto mt-4 max-w-md">
                Responses closed on {{ closesLabel }} so that final numbers could be confirmed with the venue. If you
                still hope to join us, please get in touch and we will do our best.
              </p>
              <BaseButton to="/contact" variant="primary" class="mt-8" icon="arrowRight">Get in touch</BaseButton>
            </div>

            <template v-else>
              <p class="eyebrow">RSVP</p>
              <h2 class="display-4 mt-4 text-navy-900">Kindly confirm your attendance</h2>
              <p class="body-copy mt-3">Thank you for your kind consideration of this invitation.</p>
              <p v-if="closesLabel" class="mt-4 border-l-2 border-gold-500 bg-gold-50 px-4 py-3 text-[0.875rem] font-medium leading-6 text-gold-800">
                Please respond by {{ closesLabel }}.
              </p>
              <div class="mt-9">
                <RsvpForm />
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import RsvpForm from '../../components/forms/RsvpForm.vue';
import AppIcon from '../../components/ui/AppIcon.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import { useApiPage } from '../../composables/useApiPage';
import { useCountdown } from '../../composables/useCountdown';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate } from '../../utils/format';

const { payload, loading, error, retry } = useApiPage('/launch');

const event = computed(() => payload.value?.data?.event || {});
const meta = computed(() => event.value.meta || {});

const title = computed(() => event.value.title || 'Entrusted');
const occasion = computed(() => meta.value.occasion || '');
const bookSlug = computed(() => meta.value.book_slug || 'entrusted');

const heading = computed(() => 'An evening of gratitude and great beginnings.');
const intro = computed(
  () =>
    ``,
);

const { parts, hasPassed, isToday } = useCountdown(() => meta.value.starts_at);

// RSVPs close before the evening itself, so the deadline gets its own clock.
const { hasPassed: rsvpClosed } = useCountdown(() => meta.value.rsvp_closes_at);
const closesLabel = computed(() => meta.value.rsvp_closes_label || formatDate(meta.value.rsvp_closes_at));

const countdownLabel = computed(() => {
  if (isToday.value) return 'Today';
  const { days } = parts.value;
  return days === 1 ? '1 day to go' : `${days} days to go`;
});

const details = computed(() =>
  [
    { label: 'Date', value: meta.value.date_label },
    { label: 'Time', value: meta.value.time_label },
    { label: 'Venue', value: meta.value.venue },
    { label: 'Address', value: meta.value.address },
  ].filter((detail) => Boolean(detail.value)),
);

usePageMeta(() => ({
  title: 'RSVP',
  description: `Confirm your attendance for the unveiling of ${title.value} and Femi Owoyele's fortieth birthday celebration.`,
}));
</script>
