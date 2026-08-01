<template>
  <div>
    <div class="mb-8">
      <p class="eyebrow">Overview</p>
      <h1 class="mt-3 font-serif text-4xl text-navy">Content Studio</h1>
      <p class="mt-3 max-w-2xl text-sm leading-6 text-ink/65">
        Manage articles, books, media, metrics, quotes, convictions, Build Tomorrow content, and the other CMS-backed
        entities defined in the specification.
      </p>
    </div>

    <div v-if="loading" class="text-sm text-ink/60">Loading overview...</div>
    <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <RouterLink
        v-for="item in overview"
        :key="item.label"
        :to="`/admin/content/${slugFor(item.label)}`"
        class="focus-ring border border-navy/10 bg-white p-5 hover:border-forest"
      >
        <p class="text-sm font-semibold text-forest">{{ item.label }}</p>
        <p class="mt-4 font-serif text-4xl text-navy">{{ item.count }}</p>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
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
