<template>
  <LoadingState v-if="loading" label="Loading the homepage" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <!-- ============================================================ hero -->
    <section class="surface-navy on-dark relative overflow-hidden pt-header">
      <div class="shell relative grid items-center gap-14 py-16 sm:py-20 lg:grid-cols-12 lg:gap-12 lg:py-24">
        <div class="animate-floatIn lg:col-span-7">
          <p class="eyebrow !text-gold-400">{{ hero.kicker }}</p>

          <h1 class="display-1 mt-7 text-balance text-white">{{ hero.title }}</h1>

          <p class="lead mt-8 max-w-xl text-pretty !text-white/80">{{ hero.body }}</p>

          <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
            <BaseButton to="/work" variant="gold" size="lg" icon="arrowRight">Explore the work</BaseButton>
            <BaseButton to="/about" variant="outline-light" size="lg">About Femi</BaseButton>
          </div>
        </div>

        <figure class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
          <!-- Offset gold frame: a quiet editorial device rather than a drop shadow. -->
          <span class="pointer-events-none absolute -bottom-4 -right-4 hidden h-full w-full border border-gold-500/45 sm:block"></span>
          <img
            src="/images/femi-portrait.jpg"
            srcset="/images/femi-portrait-sm.jpg 624w, /images/femi-portrait.jpg 1040w"
            sizes="(min-width: 1024px) 34vw, (min-width: 640px) 22rem, 80vw"
            alt="Portrait of Femi Owoyele"
            width="1040"
            height="1390"
            class="relative aspect-[3/4] w-full object-cover object-top"
            fetchpriority="high"
            decoding="async"
          />
          <span
            class="pointer-events-none absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-t from-navy-950/95 via-navy-950/55 to-transparent"
          ></span>
          <figcaption class="absolute inset-x-0 bottom-0 p-6">
            <p class="font-serif text-lg text-white">Oluwafemi Babatunde Owoyele</p>
            <p class="mt-1 text-micro font-semibold uppercase text-gold-400">Builder · Author · Mentor</p>
          </figcaption>
        </figure>
      </div>

      <!-- Signals strip: reads as the index of an annual report. -->
      <div class="border-t border-white/12">
        <dl class="shell grid grid-cols-2 lg:grid-cols-4">
          <div
            v-for="(signal, index) in heroSignals"
            :key="signal.label"
            :class="[
              'border-white/12 py-7 pr-6 lg:py-8',
              index % 2 === 1 && 'border-l pl-6',
              index >= 2 && 'border-t lg:border-t-0',
              index === 2 && 'lg:border-l lg:pl-6',
              index === 3 && 'lg:pl-6',
            ]"
          >
            <dt class="text-micro font-semibold uppercase text-gold-400">{{ signal.label }}</dt>
            <dd class="mt-3 text-[0.9rem] leading-6 text-white/70">{{ signal.value }}</dd>
          </div>
        </dl>
      </div>
    </section>

    <!-- ==================================================== introduction -->
    <SectionWrapper>
      <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-5">
          <p class="eyebrow">Introduction</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">{{ intro.title }}</h2>
        </div>
        <div v-reveal="80" class="lg:col-span-7">
          <p class="lead text-pretty">{{ intro.body }}</p>
          <BaseButton to="/about" variant="ghost" class="mt-8" icon="arrowRight">Read the full story</BaseButton>
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================= pillars -->
    <SectionWrapper tone="sand">
      <SectionHeading
        eyebrow="Pillars of practice"
        title="One body of work, six disciplines."
        lead="Each pillar is a working practice rather than a job title — connected by the same question: what will still stand in twenty years?"
        align="between"
      >
        <template #action>
          <BaseButton to="/work" variant="outline" icon="arrowRight">View all pillars</BaseButton>
        </template>
      </SectionHeading>

      <div class="mt-14 grid [&>*]:-mb-px [&>*]:-mr-px sm:grid-cols-2 lg:grid-cols-3">
        <PillarCard v-for="(pillar, index) in pillars" :key="pillar.slug" v-reveal="index * 60" :pillar="pillar" />
      </div>
    </SectionWrapper>

    <!-- ======================================================== featured -->
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">Latest thinking</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">Ideas worth returning to.</h2>
            <p class="body-copy mt-5">
              Essays and frameworks written for people carrying real responsibility — founders, boards, public
              institutions, and the builders coming after them.
            </p>
            <BaseButton to="/research-ideas" variant="ghost" class="mt-8" icon="arrowRight">
              All research &amp; ideas
            </BaseButton>
          </div>
        </div>

        <div class="lg:col-span-8">
          <div v-if="featuredArticles.length" class="border-b border-navy-900/12">
            <ContentCard
              v-for="(article, index) in featuredArticles"
              :key="article.slug"
              v-reveal="index * 70"
              variant="list"
              :to="`/research-ideas/${article.slug}`"
              :title="article.title"
              :description="article.excerpt"
              :meta="article.category?.name"
              :date="formatDate(article.published_at)"
              action="Read the essay"
            />
          </div>
          <EmptyState v-else title="The first essays are on their way." />
        </div>
      </div>
    </SectionWrapper>

    <!-- =========================================================== quote -->
    <QuoteBanner :quote="quote" />

    <!-- ==================================================== featured book -->
    <SectionWrapper v-if="featuredBook" tone="paper">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-20">
        <div v-reveal class="mx-auto w-full max-w-[260px] lg:col-span-4 lg:mx-0 lg:max-w-[300px]">
          <BookCover :book="featuredBook" />
        </div>
        <div v-reveal="80" class="lg:col-span-8">
          <p class="eyebrow">Authorship</p>
          <h2 class="display-3 mt-6 text-balance text-navy-900">{{ featuredBook.title }}</h2>
          <p v-if="featuredBook.subtitle" class="mt-4 font-serif text-xl text-forest-800">{{ featuredBook.subtitle }}</p>
          <p class="body-copy mt-6 max-w-2xl">{{ featuredBook.description }}</p>
          <div class="mt-9 flex flex-wrap gap-x-4 gap-y-3">
            <BaseButton :to="`/books/${featuredBook.slug}`" variant="primary" icon="arrowRight">About the book</BaseButton>
            <BaseButton to="/books" variant="outline">All books</BaseButton>
          </div>
        </div>
      </div>
    </SectionWrapper>

    <!-- ========================================================== impact -->
    <SectionWrapper tone="navy">
      <SectionHeading
        dark
        eyebrow="Impact so far"
        title="A record still being written."
        lead="Numbers are a partial picture, but they mark the shape of the work: people formed, rooms addressed, and communities reached."
        align="between"
      >
        <template #action>
          <BaseButton to="/impact" variant="outline-light" icon="arrowRight">See the full record</BaseButton>
        </template>
      </SectionHeading>

      <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
        <StatCard
          v-for="(metric, index) in impactMetrics"
          :key="metric.slug"
          v-reveal="index * 60"
          :metric="metric"
          dark
        />
      </div>
    </SectionWrapper>

    <!-- ====================================================== newsletter -->
    <section class="surface-sand">
      <div class="shell py-16 lg:py-24">
        <div v-reveal class="grid gap-12 lg:grid-cols-12 lg:items-end lg:gap-20">
          <div class="lg:col-span-6">
            <p class="eyebrow">{{ footerStatement.title }}</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">{{ footerStatement.body }}</h2>
          </div>
          <div class="lg:col-span-6">
            <p class="body-copy">
              New essays, book news, and mentorship openings — sent occasionally, and only when there is something
              worth your attention.
            </p>
            <div class="mt-8">
              <NewsletterForm source="home" hint="Occasional essays and notes. Unsubscribe at any time." />
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import BookCover from '../../components/cards/BookCover.vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import PillarCard from '../../components/cards/PillarCard.vue';
import StatCard from '../../components/cards/StatCard.vue';
import NewsletterForm from '../../components/forms/NewsletterForm.vue';
import QuoteBanner from '../../components/sections/QuoteBanner.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { formatDate } from '../../utils/format';

const { payload, loading, error, retry } = useApiPage('/home');

const data = computed(() => payload.value?.data || {});

const hero = computed(() => {
  const block = data.value.hero || {};
  return {
    kicker: block.meta?.kicker || 'Enterprise · Leadership · Stewardship',
    title: block.title || 'Building what can be trusted.',
    body:
      block.body ||
      'Four decades of work across enterprise, governance, sustainability, mentorship, and authorship — held together by one conviction: institutions outlive individuals, and they deserve to be built well.',
  };
});

const intro = computed(() => {
  const block = data.value.intro || {};
  return {
    title: block.title || 'A platform for serious work.',
    body:
      block.body ||
      'This is where the work is gathered: the companies, the essays, the books, the mentorship rooms, and the public conversations. It is designed to grow — calm in tone, rigorous in substance, rooted in African experience, and open to the world.',
  };
});

const footerStatement = computed(() => {
  const block = data.value.footer_statement || {};
  return {
    title: block.title || 'The long view',
    body: block.body || 'The work continues. Follow it as it unfolds.',
  };
});

const pillars = computed(() => data.value.pillars || []);
const featuredArticles = computed(() => data.value.featured?.articles || []);
const featuredBook = computed(() => (data.value.featured?.books || [])[0] || null);
const impactMetrics = computed(() => data.value.impact_metrics || []);
const quote = computed(() => data.value.quote || null);

const heroSignals = [
  { label: 'Practice', value: 'Enterprise, governance, and sustainability' },
  { label: 'Formation', value: 'Mentorship for the next generation of builders' },
  { label: 'Authorship', value: 'Books, essays, and public writing' },
  { label: 'Outlook', value: 'African in identity, global in conversation' },
];

usePageMeta(() => ({
  title: null,
  description: hero.value.body,
}));
</script>
