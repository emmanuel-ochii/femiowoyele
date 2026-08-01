<template>
  <header class="fixed inset-x-0 top-0 z-50 border-b border-navy/10 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-[var(--header-height)] w-full max-w-7xl items-center justify-between px-5 sm:px-6 lg:px-8">
      <RouterLink to="/" class="focus-ring font-serif text-xl text-navy">Femi Owoyele</RouterLink>

      <nav class="hidden items-center gap-6 lg:flex" aria-label="Primary navigation">
        <RouterLink
          v-for="item in primaryNav"
          :key="item.to"
          :to="item.to"
          class="focus-ring text-sm font-semibold text-ink/72 transition hover:text-forest"
          active-class="text-forest"
        >
          {{ item.label }}
        </RouterLink>
      </nav>

      <button
        class="focus-ring inline-flex h-11 w-11 shrink-0 items-center justify-center border border-navy/25 bg-white/85 lg:hidden"
        type="button"
        :aria-expanded="open"
        aria-controls="mobile-nav"
        aria-label="Toggle navigation"
        @click="open = !open"
      >
        <span class="flex w-5 flex-col gap-1.5">
          <span class="h-px bg-navy"></span>
          <span class="h-px bg-navy"></span>
          <span class="h-px bg-navy"></span>
        </span>
      </button>
    </div>

    <nav
      v-if="open"
      id="mobile-nav"
      class="border-t border-navy/10 bg-white px-5 py-5 lg:hidden"
      aria-label="Mobile navigation"
    >
      <RouterLink
        v-for="item in navigation"
        :key="item.to"
        :to="item.to"
        class="focus-ring block border-b border-navy/8 py-3 text-base font-semibold text-navy"
        @click="open = false"
      >
        {{ item.label }}
      </RouterLink>
    </nav>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useSiteStore } from '../../stores/site';

const open = ref(false);
const site = useSiteStore();
const navigation = computed(() => site.navigation);
const primaryNav = computed(() =>
  site.navigation.filter((item) =>
    ['About', 'Work', 'Build Tomorrow', 'Research & Ideas', 'Books', 'Speaking', 'Contact'].includes(item.label),
  ),
);
</script>
