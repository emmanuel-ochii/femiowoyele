<template>
  <div class="flex flex-wrap items-center gap-x-8 gap-y-3 border-y border-navy-900/10 py-4" role="group" :aria-label="label">
    <button
      v-for="option in options"
      :key="option.value"
      type="button"
      :aria-pressed="modelValue === option.value"
      :class="[
        'relative pb-1 text-[0.8rem] font-semibold uppercase tracking-[0.1em] transition-colors duration-200',
        modelValue === option.value ? 'text-forest-800' : 'text-ink-faint hover:text-navy-900',
      ]"
      @click="$emit('update:modelValue', option.value)"
    >
      {{ option.label }}
      <span
        :class="[
          'absolute inset-x-0 -bottom-[17px] h-px bg-gold-500 transition-opacity duration-200',
          modelValue === option.value ? 'opacity-100' : 'opacity-0',
        ]"
      ></span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  /** [{ label, value }] */
  options: { type: Array, required: true },
  modelValue: { type: String, default: '' },
  label: { type: String, default: 'Filter' },
});

defineEmits(['update:modelValue']);
</script>
