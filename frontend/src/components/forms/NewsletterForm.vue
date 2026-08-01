<template>
  <form class="flex flex-col gap-3 sm:flex-row" novalidate @submit="onSubmit">
    <label class="sr-only" for="newsletter-email">Email address</label>
    <input
      id="newsletter-email"
      v-model="email"
      class="focus-ring min-h-11 flex-1 border border-white/25 bg-white/10 px-4 py-3 text-white placeholder:text-white/60"
      type="email"
      placeholder="Email address"
    />
    <BaseButton type="submit" variant="gold">Subscribe</BaseButton>
  </form>
  <p v-if="errors.email" class="mt-2 text-sm text-sand">{{ errors.email }}</p>
  <p v-if="status" class="mt-2 text-sm text-sand">{{ status }}</p>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { ref } from 'vue';
import { z } from 'zod';
import { contentApi } from '../../services/contentApi';
import BaseButton from '../ui/BaseButton.vue';

const schema = toTypedSchema(z.object({ email: z.string().email('Enter a valid email.') }));
const { errors, handleSubmit, resetForm } = useForm({ validationSchema: schema, initialValues: { email: '' } });
const { value: email } = useField('email');
const status = ref('');

const onSubmit = handleSubmit(async (values) => {
  await contentApi.post('/newsletter/subscribe', { ...values, source: 'footer' });
  status.value = 'Subscribed.';
  resetForm();
});
</script>
