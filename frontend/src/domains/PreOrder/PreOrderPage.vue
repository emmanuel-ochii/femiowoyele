<template>
  <LoadingState v-if="loading" label="Loading the pre-order" />
  <ErrorState v-else-if="error" :on-retry="retry" title="The pre-order page could not be loaded." />

  <template v-else>
    <section class="surface-navy on-dark relative overflow-hidden pt-header">
      <div class="shell relative py-14 lg:py-20">
        <TestModeBanner v-if="isTestMode" class="mb-10" />

        <div class="grid gap-12 lg:grid-cols-12 lg:items-center lg:gap-16">
          <!-- ------------------------------------------------------- the book -->
          <div class="lg:col-span-7">
            <p class="eyebrow !text-gold-400">Entrusted book</p>
            <h1 class="display-1 mt-6 max-w-4xl text-balance text-white">Meet {{ book.title || 'Entrusted' }}.</h1>
            <p v-if="book.subtitle" class="mt-5 max-w-2xl font-serif text-2xl italic leading-snug text-gold-300">
              {{ book.subtitle }}
            </p>

            <div class="mt-9 max-w-3xl space-y-6 text-[1.04rem] leading-[1.85] text-white/76">
              <p>
                Over the years, Femi Owoyele has written, taught, spoken, mentored, built and served across publishing,
                leadership, faith, enterprise and impact. Along the way, he has gathered lessons from successes,
                failures, people and experiences.
              </p>
              <p>
                Now, as he approaches 40, some of those convictions come together in his first book:
                <span class="font-serif text-xl italic text-gold-300">Entrusted</span>.
              </p>
            </div>

            <div class="mt-10 border-y border-white/15 py-8">
              <p class="max-w-3xl font-serif text-2xl leading-snug text-white sm:text-3xl">
                What we have is not ultimately ours; it has been placed in our hands for a purpose.
              </p>
              <div class="mt-7 flex flex-wrap gap-2.5">
                <span
                  v-for="asset in entrustedAssets"
                  :key="asset"
                  class="border border-white/15 px-3 py-1.5 text-[0.72rem] font-semibold uppercase tracking-[0.14em] text-white/68"
                >
                  {{ asset }}
                </span>
              </div>
            </div>

            <p class="mt-8 max-w-3xl text-[1.04rem] leading-[1.85] text-white/76">
              <span class="font-serif text-xl text-white">Entrusted</span> is an invitation to live intentionally,
              embrace responsibility, steward what we have well, build beyond ourselves, and make our lives count.
            </p>

            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
              <BaseButton href="#preorder-form" variant="gold" size="lg" icon="arrowRight">Reserve your copy</BaseButton>
              <p class="text-[0.85rem] leading-6 text-white/58">
                Official unveiling: <span class="font-medium text-white">18 August 2026</span>, Femi Owoyele's 40th
                birthday.
              </p>
            </div>
          </div>

          <div class="lg:col-span-5">
            <div class="mx-auto max-w-sm lg:ml-auto">
              <figure v-reveal class="relative">
                <div class="relative isolate overflow-hidden border border-gold-500/45 bg-sand-50 px-3 py-4 shadow-frame">
                  <img
                    src="/images/entrusted-mock.jpg"
                    srcset="/images/entrusted-mock-sm.jpg 700w, /images/entrusted-mock.jpg 1200w"
                    sizes="(min-width: 1024px) 24rem, 72vw"
                    :alt="`Cover of ${book.title || 'Entrusted'}`"
                    width="1200"
                    height="1709"
                    class="w-full mix-blend-multiply"
                    decoding="async"
                  />
                </div>
              </figure>

              <dl class="mt-8 grid gap-5 border-t border-white/15 pt-7">
                <div class="flex items-baseline justify-between gap-4">
                  <dt class="text-micro font-semibold uppercase text-gold-400">Price per copy</dt>
                  <dd class="font-serif text-2xl text-white">{{ pricing.unit_display }}</dd>
                </div>
                <div>
                  <dt class="text-micro font-semibold uppercase text-gold-400">Pre-order gift</dt>
                  <dd class="mt-2 text-[0.95rem] leading-7 text-white/70">
                    Every pre-order comes with a special gift from Femi.
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <div class="mt-14 flex flex-wrap gap-x-3 gap-y-2 border-t border-white/12 pt-7">
          <span v-for="tag in entrustedTags" :key="tag" class="text-[0.78rem] font-semibold text-gold-300/86">
            {{ tag }}
          </span>
        </div>
      </div>
    </section>

    <section id="preorder-form" class="surface-sand">
      <div class="shell grid gap-10 py-14 lg:grid-cols-12 lg:gap-16 lg:py-20">
        <div class="lg:col-span-5">
          <p class="eyebrow">Pre-order</p>
          <h2 class="display-3 mt-5 max-w-xl text-balance text-navy-900">Reserve a copy of Entrusted.</h2>
          <p class="body-copy mt-5 max-w-xl">
            Pre-orders are confirmed on payment. You will receive a receipt by email with your reference and where to
            collect the book.
          </p>
          <div class="mt-8 border-y border-navy-900/10 py-6">
            <p class="font-serif text-xl leading-snug text-navy-900">
              Officially unveiled on 18 August 2026 as Femi marks his 40th birthday.
            </p>
            <!-- <p class="mt-4 text-[0.95rem] leading-7 text-ink-muted">
              Copies are collected in person. Pickup points are shown as soon as your payment is confirmed.
            </p> -->
          </div>
        </div>

        <!-- ------------------------------------------------------- the form -->
        <div class="lg:col-span-7">
          <div class="on-light border border-navy-900/10 bg-white p-7 shadow-frame sm:p-10">
            <p class="eyebrow">Your details</p>
            <h3 class="display-4 mt-4 text-navy-900">Continue to payment</h3>

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
    </section>
  </template>
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
const entrustedAssets = ['Time', 'Gifts', 'Opportunities', 'Knowledge', 'Influence', 'Resources', 'Experiences', 'Life'];
const entrustedTags = [
  '#Entrusted',
  '#EntrustedBook',
  '#FemiOwoyele',
  '#FemiAt40',
  '#Stewardship',
  '#Purpose',
  '#BuildTomorrow',
];

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
