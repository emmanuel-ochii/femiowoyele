<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <section class="relative min-h-[78vh] overflow-hidden pt-[var(--header-height)] text-white">
      <img
        :src="hero.meta?.image || '/images/femi-hero.png'"
        alt="Editorial lecture setting with notes, microphone, and audience"
        class="absolute inset-0 h-full w-full object-cover"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-navy/92 via-navy/56 to-navy/10"></div>
      <div class="relative mx-auto flex min-h-[calc(78vh-var(--header-height))] max-w-7xl items-center px-5 py-16 sm:px-6 lg:px-8">
        <div class="home-hero-copy min-w-0 animate-floatIn">
          <p class="eyebrow text-gold">{{ hero.meta?.kicker }}</p>
          <h1 class="heading-xl mt-5">{{ hero.title }}</h1>
          <p class="mt-7 max-w-full break-words text-base leading-8 text-white/86 sm:max-w-2xl sm:text-lg">{{ hero.body }}</p>
          <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <BaseButton to="/work" variant="gold">Explore the work</BaseButton>
            <BaseButton to="/research-ideas" variant="secondary">Read ideas</BaseButton>
          </div>
        </div>
      </div>
    </section>

    <SectionWrapper>
      <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr]">
        <div>
          <p class="eyebrow">Introduction</p>
          <h2 class="heading-md mt-4 text-navy">{{ intro.title }}</h2>
        </div>
        <p class="prose-content">{{ intro.body }}</p>
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
        <PillarCard v-for="pillar in data.pillars" :key="pillar.slug" :pillar="pillar" />
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
            v-for="article in data.featured.articles"
            :key="article.slug"
            :to="`/research-ideas/${article.slug}`"
            :title="article.title"
            :description="article.excerpt"
            :meta="article.category?.name"
          />
        </div>
      </div>
    </SectionWrapper>

    <QuoteBanner :quote="data.quote" />

    <SectionWrapper>
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard v-for="metric in data.impact_metrics" :key="metric.slug" :metric="metric" />
      </div>
    </SectionWrapper>

    <SectionWrapper tone="navy">
      <div class="grid gap-8 lg:grid-cols-[1fr_0.8fr] lg:items-center">
        <div>
          <p class="eyebrow text-gold">Footer Statement</p>
          <h2 class="heading-md mt-4">{{ data.footer_statement.title }}</h2>
          <p class="mt-5 max-w-3xl text-lg leading-8 text-white/78">{{ data.footer_statement.body }}</p>
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
const data = computed(() => payload.value?.data || {});
const hero = computed(() => data.value.hero || {});
const intro = computed(() => data.value.intro || {});
</script>
