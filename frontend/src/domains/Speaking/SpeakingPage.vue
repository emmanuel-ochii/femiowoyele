<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Speaking"
      title="Serious conversations for serious rooms."
      description="Topics, audiences, engagements, media, and enquiries for keynotes, panels, lectures, and private sessions."
    />
    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="grid gap-8">
          <article v-for="block in content" :key="block.slug">
            <h2 class="font-serif text-3xl text-navy">{{ block.title }}</h2>
            <p class="prose-content mt-4">{{ block.body }}</p>
            <div v-if="block.meta?.audiences" class="mt-5 flex flex-wrap gap-2">
              <span v-for="audience in block.meta.audiences" :key="audience" class="bg-sand px-3 py-1 text-sm text-navy">
                {{ audience }}
              </span>
            </div>
          </article>
        </div>
        <div class="border border-navy/10 bg-white p-6 shadow-soft">
          <h2 class="font-serif text-3xl text-navy">Speaking enquiry</h2>
          <p class="mt-3 text-sm leading-6 text-ink/65">Share the audience, context, dates, and desired format.</p>
          <div class="mt-6">
            <ContactForm default-type="speaking" />
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
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/speaking');
const content = computed(() => payload.value?.data?.content || []);
</script>
