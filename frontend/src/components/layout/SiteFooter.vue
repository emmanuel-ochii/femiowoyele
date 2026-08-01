<template>
  <footer class="border-t border-navy/10 bg-mist px-5 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1.2fr_1fr]">
      <div>
        <RouterLink to="/" class="font-serif text-2xl text-navy">Femi Owoyele</RouterLink>
        <p class="mt-5 max-w-xl text-sm leading-7 text-ink/70">
          A calm, evolving home for enterprise, leadership, governance, sustainability, mentorship, scholarship,
          authorship, and public engagement.
        </p>
      </div>
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <RouterLink
          v-for="item in navigation"
          :key="item.to"
          :to="item.to"
          class="focus-ring text-sm font-semibold text-ink/70 hover:text-forest"
        >
          {{ item.label }}
        </RouterLink>
      </div>
    </div>
    <div class="mx-auto mt-10 flex max-w-7xl flex-col gap-3 border-t border-navy/10 pt-6 text-xs text-ink/55 sm:flex-row sm:justify-between">
      <p>&copy; {{ year }} FemiOwoyele.com</p>
      <RouterLink to="/admin" class="focus-ring hover:text-forest">Admin</RouterLink>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';
import { useSiteStore } from '../../stores/site';

const year = new Date().getFullYear();
const navigation = computed(() => useSiteStore().navigation);
</script>
