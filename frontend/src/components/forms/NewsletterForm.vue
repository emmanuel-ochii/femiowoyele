<template>
  <form class="w-full" novalidate @submit="onSubmit">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div class="flex-1">
        <label class="field-label" for="newsletter-email">Email address</label>
        <input
          id="newsletter-email"
          v-model="email"
          class="field-input mt-2"
          :class="errors.email && 'field-input-invalid'"
          type="email"
          autocomplete="email"
          placeholder="you@organisation.com"
          :aria-invalid="Boolean(errors.email)"
          aria-describedby="newsletter-status"
        />
      </div>
      <BaseButton type="submit" variant="gold" :loading="isSubmitting" icon="arrowRight">
        {{ isSubmitting ? 'Subscribing' : 'Subscribe' }}
      </BaseButton>
    </div>

    <p id="newsletter-status" class="mt-3 min-h-5 text-[0.8rem]" aria-live="polite">
      <span v-if="errors.email" class="text-red-400">{{ errors.email }}</span>
      <span v-else-if="serverError" class="text-red-400">{{ serverError }}</span>
      <span v-else-if="status" class="text-gold-300">{{ status }}</span>
      <span v-else class="text-white/45">{{ hint }}</span>
    </p>
  </form>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { ref } from 'vue';
import { z } from 'zod';
import { contentApi } from '../../services/contentApi';
import BaseButton from '../ui/BaseButton.vue';

const props = defineProps({
  source: { type: String, default: 'footer' },
  hint: { type: String, default: 'Occasional essays and notes. No noise, and you can unsubscribe at any time.' },
});

const schema = toTypedSchema(z.object({ email: z.string().min(1, 'Enter your email address.').email('Enter a valid email address.') }));
const { errors, handleSubmit, isSubmitting, resetForm } = useForm({
  validationSchema: schema,
  initialValues: { email: '' },
});
const { value: email } = useField('email');
const status = ref('');
const serverError = ref('');

const onSubmit = handleSubmit(async (values) => {
  status.value = '';
  serverError.value = '';

  try {
    await contentApi.post('/newsletter/subscribe', { ...values, source: props.source });
    status.value = 'Thank you — you are on the list.';
    resetForm();
  } catch (error) {
    serverError.value =
      error.response?.data?.message || 'That could not be submitted right now. Please try again shortly.';
  }
});
</script>
