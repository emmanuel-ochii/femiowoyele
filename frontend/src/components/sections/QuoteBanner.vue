<template>
  <section v-if="quote?.text" class="surface-navy on-dark">
    <div class="shell py-20 lg:py-28">
      <figure v-reveal class="mx-auto max-w-4xl text-center">
        <span aria-hidden="true" class="block font-serif text-6xl leading-none text-gold-500/70">&ldquo;</span>
        <blockquote class="mt-4">
          <p class="font-serif text-[clamp(1.65rem,1.1rem+2.1vw,3rem)] leading-[1.28] text-balance text-white">
            {{ quote.text }}
          </p>
        </blockquote>
        <figcaption v-if="quote.source" class="mt-9">
          <span class="mx-auto mb-5 block h-px w-14 bg-gold-500"></span>
          <span class="text-micro font-semibold uppercase text-white/55">{{ quote.source }}</span>
        </figcaption>
      </figure>
    </div>
  </section>
</template>

<script setup>
defineProps({
  quote: { type: Object, default: null },
});
</script>
