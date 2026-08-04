<template>
  <div class="rich-text">
    <p v-for="(paragraph, index) in paragraphs" :key="index">{{ paragraph }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  body: { type: String, default: '' },
});

/**
 * Splits stored copy into paragraphs on blank lines (falling back to single
 * newlines). Rendered as text nodes, so author-supplied markup can never be
 * injected into the page.
 */
const paragraphs = computed(() => {
  const raw = String(props.body || '').trim();
  if (!raw) return [];

  const blocks = raw.split(/\n\s*\n/);
  return (blocks.length > 1 ? blocks : raw.split(/\n/)).map((block) => block.trim()).filter(Boolean);
});
</script>
