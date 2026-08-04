<template>
  <section class="surface-navy on-dark relative overflow-hidden pt-header">
    <div class="shell relative grid gap-12 py-16 sm:py-20 lg:grid-cols-12 lg:items-end lg:gap-16 lg:py-24">
      <div :class="['animate-floatIn', image ? 'lg:col-span-7' : 'lg:col-span-9']">
        <nav v-if="breadcrumb" class="mb-8 flex items-center gap-2 text-micro font-semibold uppercase text-white/40" aria-label="Breadcrumb">
          <RouterLink :to="breadcrumb.to" class="transition-colors hover:text-gold-300">{{ breadcrumb.label }}</RouterLink>
          <span aria-hidden="true">/</span>
          <span class="text-white/70">{{ eyebrow }}</span>
        </nav>
        <p v-else-if="eyebrow" class="eyebrow !text-gold-400">{{ eyebrow }}</p>

        <h1 class="display-2 mt-6 max-w-4xl text-balance text-white">{{ title }}</h1>
        <p v-if="description" class="lead mt-7 max-w-2xl text-pretty !text-white/72">{{ description }}</p>

        <div v-if="$slots.actions" class="mt-10 flex flex-wrap gap-x-4 gap-y-3">
          <slot name="actions" />
        </div>
      </div>

      <div v-if="image" class="lg:col-span-5">
        <figure class="relative">
          <span class="pointer-events-none absolute -bottom-3 -right-3 hidden h-full w-full border border-gold-500/40 sm:block"></span>
          <img
            :src="image"
            :alt="imageAlt"
            class="relative aspect-[4/3] w-full object-cover"
            loading="lazy"
            decoding="async"
          />
        </figure>
      </div>
    </div>

    <div class="shell">
      <div class="hairline"></div>
    </div>

    <div v-if="$slots.meta" class="shell py-6">
      <slot name="meta" />
    </div>
  </section>
</template>

<script setup>
defineProps({
  eyebrow: { type: String, default: '' },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  image: { type: String, default: '' },
  imageAlt: { type: String, default: '' },
  /** { label, to } — renders "Parent / Eyebrow" instead of a bare eyebrow. */
  breadcrumb: { type: Object, default: null },
});
</script>
