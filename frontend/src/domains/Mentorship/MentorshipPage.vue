<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" />
  <div v-else>
    <PageHero
      eyebrow="Mentorship"
      title="Building Builders."
      description="A mentorship space for resources, programmes, applications, and the formation of emerging leaders."
    />
    <SectionWrapper tone="sand">
      <div class="grid gap-12 lg:grid-cols-[1fr_0.95fr]">
        <div class="grid gap-6">
          <ContentCard
            v-for="block in content"
            :key="block.slug"
            :title="block.title"
            :description="block.body"
            meta="Mentorship"
          />
        </div>
        <div class="bg-white p-6 shadow-soft">
          <h2 class="font-serif text-3xl text-navy">Mentorship application</h2>
          <p class="mt-3 text-sm leading-6 text-ink/65">Use this form to share context, goals, and the kind of support being requested.</p>
          <div class="mt-6">
            <ContactForm default-type="mentorship" />
          </div>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ContentCard from '../../components/cards/ContentCard.vue';
import ContactForm from '../../components/forms/ContactForm.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';

const { payload, loading, error } = useApiPage('/mentorship');
const content = computed(() => payload.value?.data?.content || []);
</script>
