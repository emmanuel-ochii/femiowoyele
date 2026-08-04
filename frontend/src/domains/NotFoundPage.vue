<template>
  <div>
    <PageHero
      eyebrow="404"
      title="This page could not be found."
      description="The link may be out of date, or the page may have moved as the site has grown. The main sections are below."
    >
      <template #actions>
        <BaseButton to="/" variant="gold" icon="arrowRight">Return home</BaseButton>
        <BaseButton to="/contact" variant="outline-light">Report a broken link</BaseButton>
      </template>
    </PageHero>

    <SectionWrapper>
      <p class="eyebrow">Where to go next</p>
      <!-- Overlapping hairlines rather than a gap-fill: a partial final row
           leaves no empty cell behind. -->
      <nav class="mt-10 grid [&>*]:-mb-px [&>*]:-mr-px sm:grid-cols-2 lg:grid-cols-3" aria-label="Site sections">
        <RouterLink
          v-for="item in navigation"
          :key="item.to"
          :to="item.to"
          class="group flex items-center justify-between gap-4 border border-navy-900/10 bg-white px-6 py-6 transition-colors duration-300 hover:bg-sand-50"
        >
          <span class="font-serif text-xl text-navy-900 transition-colors group-hover:text-forest-800">
            {{ item.label }}
          </span>
          <AppIcon
            name="arrowRight"
            :size="16"
            class="shrink-0 text-gold-600 transition-transform duration-300 ease-editorial group-hover:translate-x-1"
          />
        </RouterLink>
      </nav>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import PageHero from '../components/sections/PageHero.vue';
import AppIcon from '../components/ui/AppIcon.vue';
import BaseButton from '../components/ui/BaseButton.vue';
import SectionWrapper from '../components/ui/SectionWrapper.vue';
import { usePageMeta } from '../composables/usePageMeta';
import { useSiteStore } from '../stores/site';

const navigation = computed(() => useSiteStore().navigation);

usePageMeta({ title: 'Page not found', description: 'The requested page could not be found.' });
</script>
