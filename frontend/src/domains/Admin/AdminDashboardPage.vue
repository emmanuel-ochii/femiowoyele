<template>
  <div>
    <header class="mb-10">
      <p class="eyebrow">Overview</p>
      <h1 class="display-3 mt-5 text-navy-900">Content studio</h1>
      <p class="body-copy mt-4 max-w-2xl">
        Everything on the public site is edited here — essays, journal entries, books, media, pillars, impact figures,
        quotes, convictions, and the page copy behind each section.
      </p>
    </header>

    <div v-if="loading" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div v-for="n in 8" :key="n" class="border border-navy-900/10 bg-white p-6">
        <div class="skeleton h-3 w-20"></div>
        <div class="skeleton mt-6 h-9 w-12"></div>
      </div>
    </div>

    <div v-else class="grid [&>*]:-mb-px [&>*]:-mr-px sm:grid-cols-2 xl:grid-cols-4">
      <RouterLink
        v-for="item in overview"
        :key="item.label"
        :to="`/admin/content/${slugFor(item.label)}`"
        class="group border border-navy-900/10 bg-white p-6 transition-colors duration-300 hover:bg-sand-50"
      >
        <p class="text-micro font-semibold uppercase text-forest-700">{{ item.label }}</p>
        <p class="mt-5 font-serif text-4xl tabular-nums text-navy-900">{{ item.count }}</p>
        <span
          class="mt-5 inline-flex items-center gap-2 text-micro font-semibold uppercase text-navy-900/40 transition-colors group-hover:text-forest-800"
        >
          Manage
          <AppIcon name="arrowRight" :size="13" class="transition-transform duration-300 group-hover:translate-x-1" />
        </span>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import AppIcon from '../../components/ui/AppIcon.vue';
import { adminApi } from '../../services/adminApi';
import { adminResources } from './adminResources';

const overview = ref([]);
const loading = ref(true);

const slugFor = (label) => adminResources.find((resource) => resource.label === label)?.slug || 'articles';

onMounted(async () => {
  overview.value = await adminApi.overview();
  loading.value = false;
});
</script>
