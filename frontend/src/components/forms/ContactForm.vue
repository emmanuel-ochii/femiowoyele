<template>
  <form class="grid gap-8" novalidate @submit="onSubmit">
    <div class="grid gap-8 sm:grid-cols-2">
      <div>
        <label class="field-label" for="contact-name">Full name</label>
        <input
          id="contact-name"
          v-model="name"
          class="field-input mt-2"
          :class="errors.name && 'field-input-invalid'"
          type="text"
          autocomplete="name"
          :aria-invalid="Boolean(errors.name)"
          :aria-describedby="errors.name ? 'err-name' : undefined"
        />
        <p v-if="errors.name" id="err-name" class="field-error">{{ errors.name }}</p>
      </div>

      <div>
        <label class="field-label" for="contact-email">Email address</label>
        <input
          id="contact-email"
          v-model="email"
          class="field-input mt-2"
          :class="errors.email && 'field-input-invalid'"
          type="email"
          autocomplete="email"
          :aria-invalid="Boolean(errors.email)"
          :aria-describedby="errors.email ? 'err-email' : undefined"
        />
        <p v-if="errors.email" id="err-email" class="field-error">{{ errors.email }}</p>
      </div>
    </div>

    <div class="grid gap-8 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
      <div>
        <label class="field-label" for="contact-type">Nature of enquiry</label>
        <select
          id="contact-type"
          v-model="type"
          class="field-input mt-2 appearance-none bg-[right_0.25rem_center] bg-no-repeat pr-8"
          :class="errors.type && 'field-input-invalid'"
        >
          <option v-for="option in enquiryTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>
        <p v-if="errors.type" class="field-error">{{ errors.type }}</p>
      </div>

      <div>
        <label class="field-label" for="contact-subject">Subject</label>
        <input
          id="contact-subject"
          v-model="subject"
          class="field-input mt-2"
          :class="errors.subject && 'field-input-invalid'"
          type="text"
          :placeholder="subjectPlaceholder"
          :aria-invalid="Boolean(errors.subject)"
          :aria-describedby="errors.subject ? 'err-subject' : undefined"
        />
        <p v-if="errors.subject" id="err-subject" class="field-error">{{ errors.subject }}</p>
      </div>
    </div>

    <div>
      <div class="flex items-baseline justify-between gap-4">
        <label class="field-label" for="contact-message">Message</label>
        <span class="text-[0.7rem] tabular-nums text-ink-faint">{{ messageLength }} / 5000</span>
      </div>
      <textarea
        id="contact-message"
        v-model="message"
        class="field-input mt-2 min-h-36 resize-y"
        :class="errors.message && 'field-input-invalid'"
        :placeholder="messagePlaceholder"
        :aria-invalid="Boolean(errors.message)"
        :aria-describedby="errors.message ? 'err-message' : 'hint-message'"
      />
      <p v-if="errors.message" id="err-message" class="field-error">{{ errors.message }}</p>
      <p v-else id="hint-message" class="form-note-muted mt-2 text-[0.78rem] leading-6">
        {{ messageHint }}
      </p>
    </div>

    <div class="form-divider flex flex-col gap-4 border-t border-navy-900/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
      <BaseButton type="submit" :variant="submitVariant" size="lg" :loading="isSubmitting" icon="arrowRight">
        {{ isSubmitting ? 'Sending' : submitLabel }}
      </BaseButton>
      <p class="form-note text-[0.82rem] leading-6" aria-live="polite">
        <span v-if="status" class="form-note-success">{{ status }}</span>
        <span v-else-if="serverError" class="form-note-error">{{ serverError }}</span>
        <span v-else class="form-note-muted">Replies usually follow within a few working days.</span>
      </p>
    </div>
  </form>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import { z } from 'zod';
import { contentApi } from '../../services/contentApi';
import BaseButton from '../ui/BaseButton.vue';

const props = defineProps({
  defaultType: { type: String, default: 'general' },
  subjectPlaceholder: { type: String, default: 'A short summary' },
  messagePlaceholder: { type: String, default: 'Share the purpose, audience, timeline, and any institutional context.' },
  messageHint: {
    type: String,
    default: 'Context on the audience, timeline, and outcome you have in mind makes a reply far more useful.',
  },
  submitLabel: { type: String, default: 'Send enquiry' },
  /** Use `gold` when the form sits on a deep navy panel. */
  submitVariant: { type: String, default: 'primary' },
});

const enquiryTypes = [
  { value: 'general', label: 'General enquiry' },
  { value: 'books', label: 'Book or pre-order enquiry' },
  { value: 'speaking', label: 'Speaking invitation' },
  { value: 'mentorship', label: 'Mentorship' },
  { value: 'consulting', label: 'Advisory or consulting' },
  { value: 'research', label: 'Research collaboration' },
  { value: 'partnership', label: 'Partnership' },
  { value: 'media', label: 'Media or press' },
];

const schema = toTypedSchema(
  z.object({
    name: z.string().min(2, 'Please enter your name.'),
    email: z.string().min(1, 'Please enter your email address.').email('Please enter a valid email address.'),
    subject: z.string().min(4, 'Add a short subject line.'),
    type: z.enum(['speaking', 'consulting', 'research', 'partnership', 'media', 'mentorship', 'books', 'general']),
    message: z.string().min(20, 'A little more context helps — 20 characters or more.').max(5000, 'Please keep this under 5000 characters.'),
  }),
);

const { errors, handleSubmit, isSubmitting, resetForm, setFieldValue } = useForm({
  validationSchema: schema,
  initialValues: { name: '', email: '', subject: '', type: props.defaultType, message: '' },
});

const { value: name } = useField('name');
const { value: email } = useField('email');
const { value: subject } = useField('subject');
const { value: type } = useField('type');
const { value: message } = useField('message');
const status = ref('');
const serverError = ref('');
const messageLength = computed(() => (message.value || '').length);

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
    serverError.value =
      error.response?.data?.message || 'This message could not be sent right now. Please try again shortly.';
  }
});
</script>
