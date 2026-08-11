<template>
  <section v-if="launch" id="entrusted-book" class="surface-navy on-dark relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -left-40 top-0 h-[38rem] w-[38rem] rounded-full bg-gold-500/12 blur-[120px]"></div>
      <div class="absolute -right-32 bottom-0 h-[32rem] w-[32rem] rounded-full bg-gold-400/10 blur-[110px]"></div>
    </div>

    <div class="shell relative py-20 lg:py-28">
      <div v-reveal class="flex flex-col gap-4 border-b border-white/15 pb-8 sm:flex-row sm:items-end sm:justify-between">
        <p class="eyebrow !text-gold-400">Featured book</p>
        <p class="font-serif text-lg italic text-sand-300">{{ tagline }}</p>
      </div>

      <div class="mt-14 grid gap-14 lg:grid-cols-12 lg:items-center lg:gap-16">
        <figure v-reveal class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
          <div class="relative isolate overflow-hidden border border-gold-500/45 px-3 py-4 shadow-frame sm:px-5 sm:py-6">
            <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-white via-sand-50 to-sand-200"></span>
            <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-sand-300/70 to-transparent"></span>
            <img
              :src="image"
              :srcset="`/images/entrusted-mock-sm.jpg 700w, ${image} 1280w`"
              sizes="(min-width: 1024px) 34vw, (min-width: 640px) 24rem, 78vw"
              :alt="`Cover of ${title} by Femi Owoyele`"
              width="1280"
              height="1255"
              class="relative w-full"
              loading="lazy"
              decoding="async"
            />
          </div>
        </figure>

        <div class="lg:col-span-7">
          <div v-reveal="80">
            <h2 class="display-2 text-balance text-white">{{ title }}</h2>
            <p v-if="subtitle" class="mt-5 font-serif text-xl italic text-gold-300 sm:text-2xl">{{ subtitle }}</p>
            <p class="lead mt-8 max-w-2xl text-pretty !text-white/78">{{ body }}</p>
          </div>

          <div v-reveal="140" class="mt-11 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
            <BaseButton to="/pre-order" variant="gold" size="lg" icon="arrowRight">Pre-order Entrusted</BaseButton>
            <BaseButton :to="`/books/${bookSlug}`" variant="outline-light" size="lg">About the book</BaseButton>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import BaseButton from '../ui/BaseButton.vue';

const props = defineProps({
  launch: { type: Object, default: null },
});

const meta = computed(() => props.launch?.meta || {});

const title = computed(() => props.launch?.title || 'Entrusted');
const subtitle = computed(() => meta.value.subtitle || '');
const occasion = computed(() => meta.value.occasion || '');
const tagline = computed(() => meta.value.tagline || '');
const image = computed(() => meta.value.image || '/images/entrusted-mock.jpg');
const bookSlug = computed(() => meta.value.book_slug || 'entrusted');

const body = computed(() => props.launch?.body || '');
</script>
