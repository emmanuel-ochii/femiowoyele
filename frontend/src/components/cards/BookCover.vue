<template>
  <div :class="['relative aspect-[2/3] w-full select-none', shadow && 'shadow-frame']">
    <!-- A real cover when one is uploaded. `contain` on a paper-toned panel so
         both flat artwork (which fills a 2:3 frame exactly) and 3D mock-ups
         shot on white render correctly rather than being cropped. -->
    <div v-if="src" class="absolute inset-0 isolate flex items-center justify-center bg-sand-50">
      <!-- Multiply dissolves the white studio background of 3D mock-ups into the
           panel. Flat artwork fills the frame, where multiplying against a
           near-white panel is a no-op. -->
      <img
        :src="src"
        :alt="`Cover of ${book.title}`"
        class="h-full w-full object-contain mix-blend-multiply"
        loading="lazy"
        decoding="async"
      />
    </div>

    <!-- ...otherwise a typeset placeholder so unpublished titles still look considered. -->
    <div v-else class="surface-navy absolute inset-0 flex flex-col justify-center px-[11%] text-center" role="img" :aria-label="`Placeholder cover for ${book.title}`">
      <span class="pointer-events-none absolute inset-[6%] border border-gold-500/70"></span>
      <span class="pointer-events-none absolute inset-[9.5%] border border-sand-400/25"></span>
      <p class="font-serif text-[clamp(1.35rem,0.2rem+9cqw,3.25rem)] leading-[1.08] text-white">{{ book.title }}</p>
      <span class="mx-auto mt-[7%] h-0.5 w-2/5 bg-gold-500"></span>
      <p v-if="book.subtitle" class="mt-[7%] text-[clamp(0.62rem,0.1rem+2.6cqw,0.95rem)] leading-relaxed text-sand-100/85">
        {{ book.subtitle }}
      </p>
      <p class="absolute inset-x-0 bottom-[11%] text-[clamp(0.55rem,0.15rem+2cqw,0.8rem)] uppercase tracking-[0.18em] text-sand-400">
        Femi Owoyele
      </p>
    </div>

    <!-- Spine highlight sells the typeset placeholder as an object rather than a
         flat rectangle. Real artwork already has its own edge, so it is skipped. -->
    <span
      v-if="!src"
      class="pointer-events-none absolute inset-y-0 left-0 w-[7%] bg-gradient-to-r from-black/25 via-black/5 to-transparent"
    ></span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  book: { type: Object, required: true },
  shadow: { type: Boolean, default: true },
});

// The seeder ships an SVG placeholder; treat it as "no cover" so every book
// renders through the same typeset treatment until real artwork exists.
const src = computed(() => {
  const path = props.book.cover_image_path;
  return path && !path.endsWith('entrusted-cover.svg') ? path : null;
});
</script>

<style scoped>
div[role='img'] {
  container-type: inline-size;
}
</style>
