<template>
  <!-- Confirmation replaces the form: once an answer is recorded there is
       nothing useful left to do on this screen. -->
  <div v-if="submitted" class="text-center" role="status">
    <span
      :class="[
        'mx-auto flex h-16 w-16 items-center justify-center rounded-full border',
        dark ? 'border-gold-400/60 text-gold-400' : 'border-forest-700/40 text-forest-800',
      ]"
    >
      <AppIcon :name="submitted.attending ? 'check' : 'mail'" :size="26" />
    </span>

    <h3 :class="['display-4 mt-7', dark ? 'text-white' : 'text-navy-900']">{{ confirmation.title }}</h3>
    <p :class="['mx-auto mt-4 max-w-md text-[0.95rem] leading-7', dark ? 'text-white/65' : 'text-ink-muted']">
      {{ confirmation.body }}
    </p>

    <button
      type="button"
      :class="[
        'mt-8 text-micro font-semibold uppercase underline decoration-gold-400 underline-offset-[6px] transition-colors',
        dark ? 'text-white/60 hover:text-white' : 'text-ink-faint hover:text-navy-900',
      ]"
      @click="reset"
    >
      Change your response
    </button>
  </div>

  <form v-else class="grid gap-8" novalidate @submit="onSubmit">
    <div class="grid gap-8 sm:grid-cols-2">
      <div>
        <label class="field-label" for="rsvp-name">Full name</label>
        <input
          id="rsvp-name"
          v-model="name"
          class="field-input mt-2"
          :class="errors.name && 'field-input-invalid'"
          type="text"
          autocomplete="name"
          :aria-invalid="Boolean(errors.name)"
          :aria-describedby="errors.name ? 'rsvp-err-name' : undefined"
        />
        <p v-if="errors.name" id="rsvp-err-name" class="field-error">{{ errors.name }}</p>
      </div>

      <div>
        <label class="field-label" for="rsvp-email">Email address</label>
        <input
          id="rsvp-email"
          v-model="email"
          class="field-input mt-2"
          :class="errors.email && 'field-input-invalid'"
          type="email"
          autocomplete="email"
          :aria-invalid="Boolean(errors.email)"
          :aria-describedby="errors.email ? 'rsvp-err-email' : undefined"
        />
        <p v-if="errors.email" id="rsvp-err-email" class="field-error">{{ errors.email }}</p>
      </div>
    </div>

    <!-- Two large targets rather than a dropdown: this is the one answer that
         matters, and it should be a single tap on a phone. -->
    <fieldset>
      <legend class="field-label">Will you be attending?</legend>
      <div class="mt-3 grid gap-3 sm:grid-cols-2">
        <label
          v-for="choice in choices"
          :key="String(choice.value)"
          :class="[
            'flex cursor-pointer items-center gap-3.5 border p-4 transition-colors duration-200',
            attending === choice.value
              ? dark
                ? 'border-gold-400 bg-gold-500/12'
                : 'border-forest-800 bg-forest-50'
              : dark
                ? 'border-white/20 hover:border-white/40'
                : 'border-navy-900/15 hover:border-navy-900/35',
          ]"
        >
          <input v-model="attending" class="sr-only" type="radio" name="attending" :value="choice.value" />
          <span
            :class="[
              'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition-colors',
              attending === choice.value
                ? dark
                  ? 'border-gold-400 bg-gold-400'
                  : 'border-forest-800 bg-forest-800'
                : dark
                  ? 'border-white/40'
                  : 'border-navy-900/30',
            ]"
          >
            <AppIcon
              v-if="attending === choice.value"
              name="check"
              :size="12"
              :class="dark ? 'text-navy-950' : 'text-white'"
              :stroke-width="3"
            />
          </span>
          <span :class="['text-[0.92rem] font-medium', dark ? 'text-white' : 'text-navy-900']">{{ choice.label }}</span>
        </label>
      </div>
      <p v-if="errors.attending" class="field-error">{{ errors.attending }}</p>
    </fieldset>

    <!-- Only relevant to people who are coming. -->
    <div v-if="attending === true" class="grid gap-8 sm:grid-cols-[minmax(0,0.6fr)_minmax(0,1.4fr)]">
      <div>
        <label class="field-label" for="rsvp-guests">Guests joining you</label>
        <input
          id="rsvp-guests"
          v-model.number="guests"
          class="field-input mt-2"
          :class="errors.guests && 'field-input-invalid'"
          type="number"
          min="0"
          max="10"
          inputmode="numeric"
        />
        <p v-if="errors.guests" class="field-error">{{ errors.guests }}</p>
      </div>
      <div>
        <label class="field-label" for="rsvp-note">Anything we should know? <span class="normal-case">(optional)</span></label>
        <input
          id="rsvp-note"
          v-model="note"
          class="field-input mt-2"
          type="text"
          placeholder="Dietary requirements, access needs, arrival time"
        />
      </div>
    </div>

    <div
      class="form-divider flex flex-col gap-4 border-t border-navy-900/10 pt-7 sm:flex-row sm:items-center sm:justify-between"
    >
      <BaseButton type="submit" :variant="dark ? 'gold' : 'primary'" size="lg" :loading="isSubmitting" icon="arrowRight">
        {{ isSubmitting ? 'Sending' : 'Send RSVP' }}
      </BaseButton>
      <p class="form-note text-[0.82rem] leading-6" aria-live="polite">
        <span v-if="serverError" class="form-note-error">{{ serverError }}</span>
        <span v-else class="form-note-muted">Your details are used only for this evening.</span>
      </p>
    </div>
  </form>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { computed, ref } from 'vue';
import { z } from 'zod';
import { contentApi } from '../../services/contentApi';
import AppIcon from '../ui/AppIcon.vue';
import BaseButton from '../ui/BaseButton.vue';

const props = defineProps({
  /** Style for a deep navy panel rather than a light one. */
  dark: { type: Boolean, default: false },
});

const choices = [
  { value: true, label: 'Yes, I will be attending' },
  { value: false, label: "I won't be able to make it" },
];

const schema = toTypedSchema(
  z.object({
    name: z.string().min(2, 'Please enter your name.'),
    email: z.string().min(1, 'Please enter your email address.').email('Please enter a valid email address.'),
    attending: z.boolean({ required_error: 'Please let us know whether you can join us.' }),
    guests: z.number().int().min(0).max(10).optional(),
    note: z.string().max(1000).optional(),
  }),
);

const initialValues = { name: '', email: '', attending: undefined, guests: 0, note: '' };
const { errors, handleSubmit, isSubmitting, resetForm } = useForm({ validationSchema: schema, initialValues });

const { value: name } = useField('name');
const { value: email } = useField('email');
const { value: attending } = useField('attending');
const { value: guests } = useField('guests');
const { value: note } = useField('note');

const serverError = ref('');
const submitted = ref(null);

const confirmation = computed(() => {
  if (!submitted.value) return { title: '', body: '' };

  if (submitted.value.attending) {
    return {
      title: submitted.value.updated ? 'Your RSVP is updated.' : 'Thank you — your seat is reserved.',
      body: 'A confirmation with arrival details will follow by email closer to the evening. We look forward to seeing you.',
    };
  }

  return {
    title: submitted.value.updated ? 'Your RSVP is updated.' : 'Thank you for letting us know.',
    body: 'You will be missed. We will make sure news of the book reaches you after the evening.',
  };
});

const reset = () => {
  submitted.value = null;
  serverError.value = '';
  resetForm({ values: initialValues });
};

const onSubmit = handleSubmit(async (values) => {
  serverError.value = '';

  try {
    const response = await contentApi.post('/rsvp', {
      name: values.name,
      email: values.email,
      attending: values.attending,
      guests: values.attending ? values.guests || 0 : 0,
      note: values.note || null,
    });

    submitted.value = { attending: values.attending, updated: Boolean(response?.meta?.updated) };
  } catch (error) {
    serverError.value =
      error.response?.data?.message || 'Your RSVP could not be sent right now. Please try again shortly.';
  }
});
</script>
