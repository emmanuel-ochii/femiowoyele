<template>
  <LoadingState v-if="loading" label="Loading the pre-order" />
  <ErrorState v-else-if="error" :on-retry="retry" title="The pre-order page could not be loaded." />

  <section v-else class="surface-navy on-dark relative overflow-hidden pt-header">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -left-48 top-0 h-[42rem] w-[42rem] rounded-full bg-gold-500/12 blur-[130px]"></div>
      <div class="absolute -right-40 bottom-0 h-[34rem] w-[34rem] rounded-full bg-gold-400/10 blur-[120px]"></div>
    </div>

    <div class="shell relative py-14 lg:py-20">
      <TestModeBanner v-if="isTestMode" class="mb-10" />

      <div class="grid gap-14 lg:grid-cols-12 lg:items-start lg:gap-16">
        <!-- ------------------------------------------------------- the book -->
        <div class="lg:col-span-5">
          <p class="eyebrow !text-gold-400">Pre-order</p>
          <h1 class="display-2 mt-6 text-balance text-white">{{ book.title || 'Entrusted' }}</h1>
          <p v-if="book.subtitle" class="mt-5 font-serif text-xl italic text-gold-300">{{ book.subtitle }}</p>

          <figure v-reveal class="relative mt-10 w-full max-w-[19rem]">
            <div class="relative isolate overflow-hidden border border-gold-500/45 px-3 py-4 shadow-frame">
              <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-white via-sand-50 to-sand-200"></span>
              <img
                src="/images/entrusted-mock.jpg"
                srcset="/images/entrusted-mock-sm.jpg 700w, /images/entrusted-mock.jpg 1200w"
                sizes="(min-width: 1024px) 20rem, 60vw"
                :alt="`Cover of ${book.title || 'Entrusted'}`"
                width="1200"
                height="1709"
                class="relative w-full mix-blend-multiply"
                decoding="async"
              />
            </div>
          </figure>

          <dl class="mt-10 space-y-5 border-t border-white/15 pt-8">
            <div class="flex items-baseline justify-between gap-4">
              <dt class="text-micro font-semibold uppercase text-gold-400">Price per copy</dt>
              <dd class="font-serif text-2xl text-white">{{ pricing.unit_display }}</dd>
            </div>
            <div>
              <dt class="text-micro font-semibold uppercase text-gold-400">Collection</dt>
              <dd class="mt-2 text-[0.95rem] leading-7 text-white/70">
                Copies are collected in person. Pickup points are shown as soon as your payment is confirmed.
              </dd>
            </div>
          </dl>
        </div>

        <!-- ------------------------------------------------------- the form -->
        <div class="lg:col-span-7">
          <div class="on-light border border-navy-900/10 bg-white p-7 shadow-frame sm:p-10">
            <p class="eyebrow">Your details</p>
            <h2 class="display-4 mt-4 text-navy-900">Reserve your copy</h2>
            <p class="body-copy mt-3">
              Pre-orders are confirmed on payment. You will receive a receipt by email with your reference and where to
              collect the book.
            </p>

            <form class="mt-9 grid gap-8" novalidate @submit="onSubmit">
              <div class="grid gap-8 sm:grid-cols-2">
                <div>
                  <label class="field-label" for="po-name">Full name</label>
                  <input
                    id="po-name"
                    v-model="name"
                    class="field-input mt-2"
                    :class="errors.name && 'field-input-invalid'"
                    type="text"
                    autocomplete="name"
                    :aria-invalid="Boolean(errors.name)"
                  />
                  <p v-if="errors.name" class="field-error">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="field-label" for="po-email">Email address</label>
                  <input
                    id="po-email"
                    v-model="email"
                    class="field-input mt-2"
                    :class="errors.email && 'field-input-invalid'"
                    type="email"
                    autocomplete="email"
                    :aria-invalid="Boolean(errors.email)"
                  />
                  <p v-if="errors.email" class="field-error">{{ errors.email }}</p>
                </div>
              </div>

              <div class="grid gap-8 sm:grid-cols-2">
                <div>
                  <label class="field-label" for="po-phone">Phone <span class="normal-case">(optional)</span></label>
                  <input id="po-phone" v-model="phone" class="field-input mt-2" type="tel" autocomplete="tel" />
                </div>
                <div>
                  <label class="field-label" for="po-quantity">Copies</label>
                  <input
                    id="po-quantity"
                    v-model.number="quantity"
                    class="field-input mt-2"
                    :class="errors.quantity && 'field-input-invalid'"
                    type="number"
                    min="1"
                    :max="pricing.max_quantity"
                    inputmode="numeric"
                  />
                  <p v-if="errors.quantity" class="field-error">{{ errors.quantity }}</p>
                </div>
              </div>

              <!-- Running total, so the amount is never a surprise at checkout. -->
              <div class="flex items-baseline justify-between gap-4 border-t border-navy-900/10 pt-7">
                <span class="text-micro font-semibold uppercase text-ink-faint">Total</span>
                <span class="font-serif text-3xl tabular-nums text-navy-900">{{ totalDisplay }}</span>
              </div>

              <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <BaseButton type="submit" variant="primary" size="lg" :loading="isSubmitting" icon="arrowRight">
                  {{ isSubmitting ? 'Preparing' : 'Continue to payment' }}
                </BaseButton>
                <p class="form-note text-[0.82rem] leading-6">
                  <span v-if="serverError" class="form-note-error">{{ serverError }}</span>
                  <span v-else class="form-note-muted">You will confirm payment on the next step.</span>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { toTypedSchema } from '@vee-validate/zod';
import { useField, useForm } from 'vee-validate';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { z } from 'zod';
import TestModeBanner from '../../components/sections/TestModeBanner.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { contentApi } from '../../services/contentApi';
import { formatMinorUnits } from '../../utils/format';

const router = useRouter();
const { payload, loading, error, retry } = useApiPage('/pre-order');

const data = computed(() => payload.value?.data || {});
const book = computed(() => data.value.book || {});
const pricing = computed(() => data.value.pricing || { unit_amount: 0, unit_display: '', max_quantity: 20 });
const isTestMode = computed(() => Boolean(data.value.payment?.is_test_mode));

const schema = toTypedSchema(
  z.object({
    name: z.string().min(2, 'Please enter your name.'),
    email: z.string().min(1, 'Please enter your email address.').email('Please enter a valid email address.'),
    phone: z.string().max(40).optional(),
    quantity: z.number({ invalid_type_error: 'Enter how many copies you want.' }).int().min(1, 'At least one copy.'),
  }),
);

const { errors, handleSubmit, isSubmitting } = useForm({
  validationSchema: schema,
  initialValues: { name: '', email: '', phone: '', quantity: 1 },
});

const { value: name } = useField('name');
const { value: email } = useField('email');
const { value: phone } = useField('phone');
const { value: quantity } = useField('quantity');
const serverError = ref('');

// Display only — the server prices the order independently on submit.
const totalDisplay = computed(() =>
  formatMinorUnits((pricing.value.unit_amount || 0) * (Number(quantity.value) || 0), pricing.value.currency),
);

const onSubmit = handleSubmit(async (values) => {
  serverError.value = '';

  try {
    const response = await contentApi.post('/pre-order', {
      name: values.name,
      email: values.email,
      phone: values.phone || null,
      quantity: values.quantity,
    });

    // Mirrors a hosted checkout: the provider tells us where to send the buyer.
    router.push(response.meta.authorization_url);
  } catch (caught) {
    serverError.value =
      caught.response?.data?.message || 'Your pre-order could not be started. Please try again shortly.';
  }
});

usePageMeta(() => ({
  title: `Pre-order ${book.value.title || 'Entrusted'}`,
  description: `Pre-order ${book.value.title || 'Entrusted'} by Femi Owoyele and collect your copy in Lagos.`,
}));
</script>
