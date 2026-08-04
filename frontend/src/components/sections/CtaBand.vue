<template>
  <section :class="[tone === 'sand' ? 'surface-sand' : 'bg-white', 'border-t border-navy-900/10']">
    <div class="shell py-16 lg:py-20">
      <div v-reveal class="grid gap-8 lg:grid-cols-[1.2fr_auto] lg:items-center lg:gap-16">
        <div>
          <p class="eyebrow">{{ eyebrow }}</p>
          <h2 class="display-4 mt-5 max-w-2xl text-balance text-navy-900">{{ title }}</h2>
          <p v-if="description" class="body-copy mt-4 max-w-xl">{{ description }}</p>
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-3">
          <BaseButton :to="primaryTo" variant="primary" size="lg" icon="arrowRight">{{ primaryLabel }}</BaseButton>
          <BaseButton v-if="secondaryTo" :to="secondaryTo" variant="outline" size="lg">{{ secondaryLabel }}</BaseButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import BaseButton from '../ui/BaseButton.vue';

defineProps({
  eyebrow: { type: String, default: 'Get in touch' },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  primaryTo: { type: String, default: '/contact' },
  primaryLabel: { type: String, default: 'Start a conversation' },
  secondaryTo: { type: String, default: '' },
  secondaryLabel: { type: String, default: '' },
  tone: { type: String, default: 'sand' },
});
</script>
