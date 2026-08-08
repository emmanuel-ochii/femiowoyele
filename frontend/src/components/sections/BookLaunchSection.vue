<template>
  <section v-if="launch" id="launch" class="surface-navy on-dark relative overflow-hidden">
    <!-- Festive register: a warm gold aurora over the brand navy, rather than a
         change of palette. Purely decorative, so it stays out of the a11y tree. -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -left-40 top-0 h-[38rem] w-[38rem] rounded-full bg-gold-500/12 blur-[120px]"></div>
      <div class="absolute -right-32 bottom-0 h-[32rem] w-[32rem] rounded-full bg-gold-400/10 blur-[110px]"></div>
    </div>

    <div class="shell relative py-20 lg:py-28">
      <!-- ----------------------------------------------------- occasion bar -->
      <div v-reveal class="flex flex-col gap-4 border-b border-white/15 pb-8 sm:flex-row sm:items-end sm:justify-between">
        <p class="eyebrow !text-gold-400">{{ hasPassed ? 'Published' : 'The unveiling' }}</p>
        <p class="font-serif text-lg italic text-sand-300">{{ occasion }}</p>
      </div>

      <div class="mt-14 grid gap-14 lg:grid-cols-12 lg:items-center lg:gap-16">
        <!-- ------------------------------------------------------- the book -->
        <figure v-reveal class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
          <!-- The mock ships on a white studio background. Multiplying it onto a
               lit panel dissolves that white and keeps the real drop shadow,
               which is both safer and better looking than keying it out. -->
          <div class="relative isolate overflow-hidden border border-gold-500/45 px-3 py-4 shadow-frame sm:px-5 sm:py-6">
            <!-- Lit-surface backdrop. It sits behind the image so the multiply
                 blend picks it up as its backdrop rather than flat white. -->
            <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-white via-sand-50 to-sand-200"></span>
            <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-sand-300/70 to-transparent"></span>
            <img
              :src="image"
              :srcset="`/images/entrusted-mock-sm.jpg 700w, ${image} 1280w`"
              sizes="(min-width: 1024px) 34vw, (min-width: 640px) 24rem, 78vw"
              :alt="`Cover of ${title} by Femi Owoyele`"
              width="1280"
              height="1255"
              class="relative w-full"
              loading="lazy"
              decoding="async"
            />
          </div>

          <!-- Fortieth-birthday seal, the one overtly celebratory mark. -->
          <div
            class="absolute -right-5 -top-7 flex h-24 w-24 flex-col items-center justify-center rounded-full border border-gold-400/60 bg-navy-950 text-center shadow-lift sm:-right-8 sm:-top-9 sm:h-28 sm:w-28"
          >
            <span class="font-serif text-3xl leading-none text-gold-400 sm:text-4xl">40</span>
            <span class="mt-1.5 text-[0.55rem] font-semibold uppercase tracking-[0.2em] text-white/60">Years</span>
          </div>
        </figure>

        <!-- ---------------------------------------------------- the details -->
        <div class="lg:col-span-7">
          <div v-reveal="80">
            <h2 class="display-2 text-balance text-white">{{ title }}</h2>
            <p v-if="subtitle" class="mt-5 font-serif text-xl italic text-gold-300 sm:text-2xl">{{ subtitle }}</p>
            <p class="lead mt-8 max-w-2xl text-pretty !text-white/78">{{ body }}</p>
            <p v-if="tagline" class="mt-4 max-w-2xl text-[0.95rem] leading-7 text-white/58">{{ tagline }}.</p>
          </div>

          <!-- Countdown before the event; a plain published line afterwards. -->
          <div v-reveal="140" class="mt-12">
            <p class="text-micro font-semibold uppercase text-gold-400">
              {{ hasPassed ? 'Unveiled' : isToday ? 'Today' : 'Counting down' }}
            </p>

            <div v-if="!hasPassed" class="mt-5 grid max-w-lg grid-cols-3 divide-x divide-white/15 border-y border-white/15">
              <div v-for="unit in countdownUnits" :key="unit.label" class="px-5 py-6 first:pl-0">
                <p class="font-serif text-4xl leading-none tabular-nums text-white sm:text-5xl">{{ unit.value }}</p>
                <p class="mt-3 text-micro font-semibold uppercase text-white/50">{{ unit.label }}</p>
              </div>
            </div>
            <p v-else class="mt-4 font-serif text-2xl text-white">
              Launched on {{ dateLabel }} in Lagos.
            </p>
          </div>

          <!-- ------------------------------------------------ where and when -->
          <!-- The countdown already closes with a rule, so the list only draws
               its own divider in the after-the-event state. -->
          <dl
            v-reveal="200"
            :class="[
              'mt-10 grid gap-x-10 gap-y-8 sm:grid-cols-2',
              hasPassed && 'border-t border-white/15 pt-8',
            ]"
          >
            <div v-for="detail in details" :key="detail.label">
              <dt class="text-micro font-semibold uppercase text-gold-400">{{ detail.label }}</dt>
              <dd class="mt-2.5 text-[0.95rem] leading-7 text-white/80">{{ detail.value }}</dd>
            </div>
          </dl>

          <div v-reveal="260" class="mt-11 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
            <!-- Before the evening the useful action is to RSVP; afterwards it
                 is to read what happened. -->
            <BaseButton :to="hasPassed ? '/entrusted' : '/rsvp'" variant="gold" size="lg" icon="arrowRight">
              {{ hasPassed ? 'Read about the evening' : 'RSVP for the evening' }}
            </BaseButton>
            <BaseButton :to="hasPassed ? `/books/${bookSlug}` : '/entrusted'" variant="outline-light" size="lg">
              {{ hasPassed ? 'About the book' : 'See the launch details' }}
            </BaseButton>
            <BaseButton v-if="rsvpPhone" :href="`tel:${rsvpPhone.replace(/\s+/g, '')}`" variant="ghost-light" size="lg">
              RSVP {{ rsvpPhone }}
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import BaseButton from '../ui/BaseButton.vue';
import { useCountdown } from '../../composables/useCountdown';

const props = defineProps({
  /** The `home.launch` content block, or null when nothing is scheduled. */
  launch: { type: Object, default: null },
});

const meta = computed(() => props.launch?.meta || {});

const title = computed(() => props.launch?.title || 'Entrusted');
const subtitle = computed(() => meta.value.subtitle || '');
const occasion = computed(() => meta.value.occasion || '');
const tagline = computed(() => meta.value.tagline || '');
const image = computed(() => meta.value.image || '/images/entrusted-mock.jpg');
const bookSlug = computed(() => meta.value.book_slug || 'entrusted');
const dateLabel = computed(() => meta.value.date_label || '');
const rsvpPhone = computed(() => meta.value.rsvp_phone || '');

const { parts, hasPassed, isToday } = useCountdown(() => meta.value.starts_at);

// The seeded copy is written in the future tense. `body_after` lets the section
// re-word itself once the evening has been and gone, without an edit on the day.
const body = computed(() =>
  hasPassed.value && meta.value.body_after ? meta.value.body_after : props.launch?.body || '',
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
</script>
