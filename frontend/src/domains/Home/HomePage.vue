<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <section class="relative isolate min-h-[86svh] overflow-hidden bg-navy pt-[var(--header-height)] text-white">
      <img
        :src="heroImage"
        alt="Portrait of Femi Owoyele"
        class="absolute inset-0 h-full w-full object-cover object-[70%_48%] sm:object-[72%_50%] lg:object-[78%_52%]"
        fetchpriority="high"
        decoding="async"
      />
      <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(11,28,50,0.98)_0%,rgba(11,28,50,0.92)_38%,rgba(11,28,50,0.58)_66%,rgba(11,28,50,0.14)_100%)]"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-[linear-gradient(0deg,rgba(11,28,50,0.94)_0%,rgba(11,28,50,0.42)_42%,rgba(11,28,50,0)_100%)]"></div>
      <div class="absolute inset-x-0 top-[var(--header-height)] h-px bg-white/14"></div>

      <div class="relative mx-auto flex min-h-[calc(86svh-var(--header-height))] max-w-7xl flex-col justify-end px-5 pb-10 pt-16 sm:px-6 sm:pb-14 lg:justify-center lg:px-8 lg:py-20">
        <div class="home-hero-copy min-w-0 animate-floatIn">
          <p class="eyebrow !text-gold">{{ heroEyebrow }}</p>
          <h1 class="heading-xl mt-5 max-w-4xl">{{ heroTitle }}</h1>
          <p class="mt-7 max-w-full break-words text-base leading-8 text-white/84 sm:max-w-2xl sm:text-lg">
            {{ heroBody }}
          </p>
          <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <BaseButton to="/work" variant="gold">Explore the work</BaseButton>
            <BaseButton to="/research-ideas" variant="secondary">Read current ideas</BaseButton>
          </div>
        </div>

        <div class="mt-10 hidden w-full max-w-[44rem] grid-cols-4 border-y border-white/16 sm:grid lg:mt-14">
          <div
            v-for="signal in heroSignals"
            :key="signal.label"
            class="min-w-0 border-l border-white/16 px-4 py-4 first:border-l-0 first:pl-0"
          >
            <p class="text-[0.68rem] font-semibold uppercase text-gold">{{ signal.label }}</p>
            <p class="mt-2 break-words text-sm leading-6 text-white/78">{{ signal.value }}</p>
          </div>
        </div>
      </div>
    </section>

    <SectionWrapper>
      <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr]">
        <div>
          <p class="eyebrow">Introduction</p>
          <h2 class="heading-md mt-4 text-navy">{{ introTitle }}</h2>
        </div>
        <p class="prose-content">{{ introBody }}</p>
      </div>
    </SectionWrapper>

    <SectionWrapper tone="sand">
      <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="eyebrow">Pillars</p>
          <h2 class="heading-md mt-4 text-navy">A coherent body of work</h2>
        </div>
        <BaseButton to="/work" variant="secondary">View all pillars</BaseButton>
      </div>
      <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <PillarCard v-for="pillar in pillars" :key="pillar.slug" :pillar="pillar" />
      </div>
    </SectionWrapper>

    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr]">
        <div>
          <p class="eyebrow">Featured</p>
          <h2 class="heading-md mt-4 text-navy">Ideas, authorship, and public record</h2>
        </div>
        <div class="grid gap-2">
          <ContentCard
            v-for="article in featured.articles"
            :key="article.slug"
            :to="`/research-ideas/${article.slug}`"
            :title="article.title"
            :description="article.excerpt"
            :meta="article.category?.name"
          />
        </div>
      </div>
    </SectionWrapper>

    <QuoteBanner :quote="quote" />

    <SectionWrapper>
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard v-for="metric in impactMetrics" :key="metric.slug" :metric="metric" />
      </div>
    </SectionWrapper>

    <SectionWrapper tone="navy">
      <div class="grid gap-8 lg:grid-cols-[1fr_0.8fr] lg:items-center">
        <div>
          <p class="eyebrow !text-gold">Footer Statement</p>
          <h2 class="heading-md mt-4">{{ footerStatement.title }}</h2>
          <p class="mt-5 max-w-3xl text-lg leading-8 text-white/78">{{ footerStatement.body }}</p>
        </div>
        <NewsletterForm />
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import PillarCard from '../../components/cards/PillarCard.vue';
import StatCard from '../../components/cards/StatCard.vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import NewsletterForm from '../../components/forms/NewsletterForm.vue';
import QuoteBanner from '../../components/sections/QuoteBanner.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/home');
const originalHeroBody =
  'A body of work at the intersection of enterprise, leadership, governance, sustainability, mentorship, scholarship, authorship, and public engagement.';
const professionalHeroBody =
  'A considered home for enterprise, governance, sustainability, mentorship, authorship, and public engagement, shaped by responsibility and institutional trust.';
const originalIntroTitle = 'Built for substance';
const originalIntroBody =
  'FemiOwoyele.com is designed as a calm, evolving home for ideas, initiatives, conversations, and institutions shaped by a long view of responsibility.';
const professionalIntroTitle = 'A platform for serious work';
const professionalIntroBody =
  'FemiOwoyele.com brings together ideas, initiatives, and records of practice across company building, public thought, mentorship, books, and civic imagination. It is designed to grow with the work: calm in tone, rigorous in substance, and global in conversation while remaining rooted in African experience.';
const originalFooterBody =
  'The work is ongoing: to build, to clarify, to mentor, and to contribute to institutions worthy of trust.';
const professionalFooterBody =
  'The work continues through enterprises, ideas, institutions, mentorship rooms, and public conversations shaped by responsibility.';
const data = computed(() => payload.value?.data || {});
const hero = computed(() => data.value.hero || {});
const intro = computed(() => data.value.intro || {});
const featured = computed(() => data.value.featured || { articles: [], books: [], media: [] });
const pillars = computed(() => data.value.pillars || []);
const impactMetrics = computed(() => data.value.impact_metrics || []);
const quote = computed(() => data.value.quote || {});
const footerStatement = computed(() => ({
  title: data.value.footer_statement?.title || 'The long view',
  body:
    !data.value.footer_statement?.body || data.value.footer_statement.body === originalFooterBody
      ? professionalFooterBody
      : data.value.footer_statement.body,
}));
const heroImage = computed(() => {
  const image = hero.value.meta?.image;

  return !image || image === '/images/femi-hero.png' ? '/images/profem.jpeg' : image;
});
const heroEyebrow = computed(() => hero.value.meta?.kicker || 'Enterprise. Leadership. Stewardship.');
const heroTitle = computed(() => hero.value.title || 'Femi Owoyele');
const heroBody = computed(() =>
  !hero.value.body || hero.value.body === originalHeroBody ? professionalHeroBody : hero.value.body,
);
const introTitle = computed(() =>
  !intro.value.title || intro.value.title === originalIntroTitle ? professionalIntroTitle : intro.value.title,
);
const introBody = computed(() =>
  !intro.value.body || intro.value.body === originalIntroBody ? professionalIntroBody : intro.value.body,
);
const heroSignals = [
  { label: 'Focus', value: 'Enterprise and governance' },
  { label: 'Formation', value: 'Builder mentorship' },
  { label: 'Authorship', value: 'Books and essays' },
  { label: 'Outlook', value: 'African roots, global reach' },
];
</script>
