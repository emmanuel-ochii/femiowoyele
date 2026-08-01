<template>
  <form class="grid gap-5" novalidate @submit="onSubmit">
    <div class="grid gap-5 sm:grid-cols-2">
      <label class="grid gap-2 text-sm font-semibold text-navy">
        Name
        <input v-model="name" class="focus-ring border border-navy/15 px-4 py-3 font-normal" type="text" />
        <span v-if="errors.name" class="text-xs text-red-700">{{ errors.name }}</span>
      </label>
      <label class="grid gap-2 text-sm font-semibold text-navy">
        Email
        <input v-model="email" class="focus-ring border border-navy/15 px-4 py-3 font-normal" type="email" />
        <span v-if="errors.email" class="text-xs text-red-700">{{ errors.email }}</span>
      </label>
    </div>

    <div class="grid gap-5 sm:grid-cols-[0.8fr_1.2fr]">
      <label class="grid gap-2 text-sm font-semibold text-navy">
        Type
        <select v-model="type" class="focus-ring border border-navy/15 px-4 py-3 font-normal">
          <option value="general">General</option>
          <option value="speaking">Speaking</option>
          <option value="mentorship">Mentorship</option>
          <option value="research">Research</option>
          <option value="partnership">Partnership</option>
          <option value="media">Media</option>
          <option value="consulting">Consulting</option>
        </select>
        <span v-if="errors.type" class="text-xs text-red-700">{{ errors.type }}</span>
      </label>
      <label class="grid gap-2 text-sm font-semibold text-navy">
        Subject
        <input v-model="subject" class="focus-ring border border-navy/15 px-4 py-3 font-normal" type="text" />
        <span v-if="errors.subject" class="text-xs text-red-700">{{ errors.subject }}</span>
      </label>
    </div>

    <label class="grid gap-2 text-sm font-semibold text-navy">
      Message
      <textarea v-model="message" class="focus-ring min-h-40 border border-navy/15 px-4 py-3 font-normal" />
      <span v-if="errors.message" class="text-xs text-red-700">{{ errors.message }}</span>
    </label>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
      <BaseButton type="submit" :disabled="isSubmitting">Send enquiry</BaseButton>
      <p v-if="status" class="text-sm text-forest">{{ status }}</p>
      <p v-if="serverError" class="text-sm text-red-700">{{ serverError }}</p>
    </div>
  </form>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { ref, watch } from 'vue';
import { z } from 'zod';
import { contentApi } from '../../services/contentApi';
import BaseButton from '../ui/BaseButton.vue';

const props = defineProps({
  defaultType: {
    type: String,
    default: 'general',
  },
});

const schema = toTypedSchema(
  z.object({
    name: z.string().min(2, 'Enter your name.'),
    email: z.string().email('Enter a valid email.'),
    subject: z.string().min(4, 'Add a short subject.'),
    type: z.enum(['speaking', 'consulting', 'research', 'partnership', 'media', 'mentorship', 'general']),
    message: z.string().min(20, 'Share a little more context.').max(5000),
  }),
);

const { errors, handleSubmit, isSubmitting, resetForm, setFieldValue } = useForm({
  validationSchema: schema,
  initialValues: {
    name: '',
    email: '',
    subject: '',
    type: props.defaultType,
    message: '',
  },
});

const { value: name } = useField('name');
const { value: email } = useField('email');
const { value: subject } = useField('subject');
const { value: type } = useField('type');
const { value: message } = useField('message');
const status = ref('');
const serverError = ref('');

watch(
  () => props.defaultType,
  (next) => setFieldValue('type', next),
);

const onSubmit = handleSubmit(async (values) => {
  status.value = '';
  serverError.value = '';

  try {
    await contentApi.post('/contact', values);
    status.value = 'Thank you. Your message has been received.';
    resetForm({ values: { name: '', email: '', subject: '', type: props.defaultType, message: '' } });
  } catch (error) {
    serverError.value = error.response?.data?.message || 'Unable to send this message right now.';
  }
});
</script>
