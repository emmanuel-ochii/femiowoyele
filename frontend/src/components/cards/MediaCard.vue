<template>
  <article
    class="group relative flex h-full flex-col border border-navy-900/10 bg-white transition-all duration-300 ease-editorial hover:-translate-y-1 hover:border-navy-900/20 hover:shadow-lift"
  >
    <div class="surface-navy relative flex aspect-[16/10] items-center justify-center overflow-hidden">
      <img
        v-if="item.thumbnail_path"
        :src="item.thumbnail_path"
        :alt="item.title"
        class="absolute inset-0 h-full w-full object-cover opacity-90 transition-transform duration-500 ease-editorial group-hover:scale-[1.03]"
        loading="lazy"
        decoding="async"
      />
      <span
        class="relative flex h-12 w-12 items-center justify-center rounded-full border border-gold-500/60 text-gold-400 transition-colors duration-300 group-hover:border-gold-400 group-hover:bg-gold-500 group-hover:text-navy-900"
      >
        <AppIcon :name="typeIcon" :size="18" />
      </span>
    </div>

    <div class="flex flex-1 flex-col p-6">
      <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
        <p class="text-micro font-semibold uppercase text-forest-700">{{ typeLabel }}</p>
        <span v-if="publishedOn" class="h-1 w-1 rounded-full bg-gold-500"></span>
        <p v-if="publishedOn" class="text-micro font-medium uppercase text-ink-faint">{{ publishedOn }}</p>
      </div>

      <h3 class="mt-4 font-serif text-xl leading-snug text-navy-900">
        <a
          v-if="item.url"
          :href="item.url"
          target="_blank"
          rel="noopener noreferrer"
          class="transition-colors duration-300 before:absolute before:inset-0 before:content-[''] hover:text-forest-800"
        >
          {{ item.title }}
        </a>
        <span v-else>{{ item.title }}</span>
      </h3>

      <p v-if="item.description" class="mt-3 line-clamp-3 text-[0.9rem] leading-7 text-ink-muted">{{ item.description }}</p>

      <span
        v-if="item.url"
        class="mt-auto pt-6 inline-flex items-center gap-2 text-micro font-semibold uppercase text-navy-900/45 transition-colors duration-300 group-hover:text-forest-800"
      >
        {{ actionLabel }}
        <AppIcon name="arrowUpRight" :size="14" />
      </span>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import AppIcon from '../ui/AppIcon.vue';
import { formatDate } from '../../utils/format';

const props = defineProps({
  item: { type: Object, required: true },
});

const publishedOn = computed(() => formatDate(props.item.published_at));

const TYPES = {
  interview: { label: 'Interview', icon: 'microphone', action: 'Read interview' },
  tv: { label: 'Television', icon: 'play', action: 'Watch' },
  podcast: { label: 'Podcast', icon: 'microphone', action: 'Listen' },
  video: { label: 'Video', icon: 'play', action: 'Watch' },
  image: { label: 'Gallery', icon: 'image', action: 'View' },
  download: { label: 'Download', icon: 'download', action: 'Download' },
};

const meta = computed(() => TYPES[props.item.type] || { label: props.item.type, icon: 'arrowUpRight', action: 'Open' });
const typeLabel = computed(() => meta.value.label);
const typeIcon = computed(() => meta.value.icon);
const actionLabel = computed(() => meta.value.action);
</script>
