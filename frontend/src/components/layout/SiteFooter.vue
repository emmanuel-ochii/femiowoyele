<template>
  <footer class="surface-navy on-dark">
    <div class="shell py-16 lg:py-20">
      <div class="grid gap-12 lg:grid-cols-[1.15fr_1.6fr] lg:gap-20">
        <div>
          <RouterLink to="/" class="font-serif text-2xl text-white">Femi Owoyele</RouterLink>
          <p class="mt-5 max-w-sm text-[0.9rem] leading-7 text-white/60">
            Enterprise, leadership, governance, sustainability, mentorship, authorship, and public engagement — held
            together by one commitment: to build what can be trusted.
          </p>
          <BaseButton to="/contact" variant="ghost-light" size="sm" icon="arrowRight" class="mt-7">
            Start a conversation
          </BaseButton>
        </div>

        <nav class="grid grid-cols-2 gap-x-8 gap-y-10 sm:grid-cols-3" aria-label="Footer">
          <div v-for="group in footerGroups" :key="group.label">
            <p class="text-micro font-semibold uppercase text-gold-400">{{ group.label }}</p>
            <ul class="mt-5 space-y-3">
              <li v-for="item in group.items" :key="item.to">
                <RouterLink
                  :to="item.to"
                  class="text-[0.9rem] text-white/70 transition-colors duration-200 hover:text-white"
                >
                  {{ item.label }}
                </RouterLink>
              </li>
            </ul>
          </div>
        </nav>
      </div>

      <div class="hairline mt-14"></div>

      <div class="mt-6 flex flex-col gap-4 text-[0.8rem] text-white/45 sm:flex-row sm:items-center sm:justify-between">
        <p>&copy; {{ year }} Femi Owoyele. All rights reserved.</p>
        <div class="flex items-center gap-6">
          <button type="button" class="inline-flex items-center gap-2 transition-colors hover:text-white" @click="toTop">
            Back to top
            <AppIcon name="arrowUp" :size="13" />
          </button>
          <RouterLink to="/admin" class="transition-colors hover:text-white">Admin</RouterLink>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';
import { useSiteStore } from '../../stores/site';
import AppIcon from '../ui/AppIcon.vue';
import BaseButton from '../ui/BaseButton.vue';

const year = new Date().getFullYear();
const footerGroups = computed(() => useSiteStore().footerGroups);

const toTop = () => {
  const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
};
</script>
