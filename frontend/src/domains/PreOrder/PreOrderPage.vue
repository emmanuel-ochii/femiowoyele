<template>
  <LoadingState v-if="loading" label="Loading the pre-order" />
  <ErrorState v-else-if="error" :on-retry="retry" title="The pre-order page could not be loaded." />

  <section v-else class="surface-navy on-dark relative overflow-hidden pt-header">
    <div class="shell relative py-10 lg:py-14">
      <TestModeBanner v-if="isTestMode" class="mb-10" />

      <div class="grid gap-8 lg:grid-cols-12 lg:items-start lg:gap-8 xl:gap-10">
        <div class="order-1 lg:col-span-3 xl:col-span-4">
          <p class="eyebrow !text-gold-400">Pre-order Entrusted</p>
          <h1 class="display-2 mt-5 max-w-3xl text-balance text-white">
            Meet <span class="italic text-gold-200">{{ book.title || 'Entrusted' }}</span>.
          </h1>
          <p class="mt-5 max-w-xl text-[1.02rem] leading-[1.75] text-white/76">
            A first book on stewardship, responsibility, and the making of a meaningful life.
          </p>

          <div class="mt-7 grid gap-3 border-y border-white/14 py-5 text-[0.88rem] text-white/68 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
            <p>
              <span class="block text-micro font-semibold uppercase text-gold-300">Pre-order gift</span>
              <span class="mt-2 block text-white">Included with every copy</span>
            </p>
            <p>
              <span class="block text-micro font-semibold uppercase text-gold-300">Secure checkout</span>
              <span class="mt-2 block text-white">Payment confirms your reservation</span>
            </p>
          </div>
        </div>

        <!-- ------------------------------------------------------- the book -->
        <div class="order-2 lg:col-span-5 xl:col-span-4">
          <figure class="relative mx-auto w-full max-w-[34rem]">
            <div class="relative isolate overflow-hidden border border-gold-500/70 bg-sand-50 p-1.5 shadow-frame sm:p-2">
              <img
                src="/images/entrusted-mock.jpg"
                srcset="/images/entrusted-mock-sm.jpg 700w, /images/entrusted-mock.jpg 1280w"
                sizes="(min-width: 1280px) 32rem, (min-width: 1024px) 38vw, 92vw"
                :alt="`Cover of ${book.title || 'Entrusted'}`"
                width="1280"
                height="1255"
                class="aspect-[1.02/1] w-full scale-[1.14] object-cover object-center"
                fetchpriority="high"
                decoding="async"
              />
            </div>
            <figcaption class="mt-5 border-l-2 border-gold-500 pl-4">
              <span class="block font-serif text-3xl leading-none text-white">Entrusted</span>
              <span class="mt-2 block text-micro font-semibold uppercase text-gold-300">
                Stewardship. Responsibility. Purpose.
              </span>
            </figcaption>
          </figure>
        </div>

        <!-- The form stays in the first section so ordering remains obvious. -->
        <aside id="preorder-form" class="order-3 lg:col-span-4">
          <div class="on-light border border-navy-900/10 bg-white p-6 shadow-frame sm:p-8 lg:sticky lg:top-[calc(var(--header-height)+1rem)]">
            <div class="flex flex-col gap-5 border-b border-navy-900/10 pb-5 sm:flex-row sm:items-start sm:justify-between">
              <div class="min-w-0">
                <p class="eyebrow">Your details</p>
                <h2 class="display-4 mt-4 text-navy-900">Reserve your copy</h2>
              </div>
              <div class="shrink-0 text-left sm:text-right">
                <p class="text-micro font-semibold uppercase text-ink-faint">Price</p>
                <p class="mt-1 font-serif text-2xl tabular-nums text-navy-900">{{ pricing.unit_display }}</p>
              </div>
            </div>

            <p class="mt-5 text-[0.9rem] leading-7 text-ink-muted">
              Pre-orders are confirmed on payment. Your receipt and collection reference will arrive by email.
            </p>

            <form class="mt-7 grid gap-6" novalidate @submit="onSubmit">
              <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
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

              <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
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
              <div class="flex items-baseline justify-between gap-4 border-t border-navy-900/10 pt-6">
                <span class="text-micro font-semibold uppercase text-ink-faint">Total</span>
                <span class="font-serif text-3xl tabular-nums text-navy-900">{{ totalDisplay }}</span>
              </div>

              <BaseButton type="submit" variant="primary" size="lg" :loading="isSubmitting" icon="arrowRight" class="w-full">
                {{ isSubmitting ? 'Preparing' : 'Continue to payment' }}
              </BaseButton>

              <p class="form-note text-[0.82rem] leading-6">
                <span v-if="serverError" class="form-note-error">{{ serverError }}</span>
                <span v-else class="form-note-muted">You will confirm payment on the next step.</span>
              </p>
            </form>
          </div>
        </aside>

        <div class="order-4 lg:col-span-12">
          <div class="grid gap-8 border-t border-white/12 pt-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] lg:gap-12">
            <div class="space-y-5 text-[0.98rem] leading-[1.78] text-white/72">
              <p>
                Over the years, Femi Owoyele has written, taught, spoken, mentored, built and served across publishing,
                leadership, faith, enterprise and impact.
              </p>
              <p>
                Those convictions now come together in his first book:
                <span class="font-serif text-lg italic text-gold-300">Entrusted</span>.
              </p>
              <p>
                Entrusted is an invitation to live intentionally, embrace responsibility, steward what we have well,
                build beyond ourselves, and make our lives count.
              </p>
            </div>

            <div>
              <div class="border-y border-white/15 py-6">
                <p class="font-serif text-xl leading-snug text-white sm:text-2xl">
                  What we have is not ultimately ours; it has been placed in our hands for a purpose.
                </p>
              </div>

              <div class="mt-7 flex flex-wrap gap-2">
                <span
                  v-for="asset in entrustedAssets"
                  :key="asset"
                  class="border border-white/15 px-2.5 py-1 text-[0.66rem] font-semibold uppercase tracking-[0.12em] text-white/62"
                >
                  {{ asset }}
                </span>
              </div>
            </div>
          </div>

          <div class="mt-8 flex flex-wrap gap-x-3 gap-y-2 border-t border-white/12 pt-6">
            <span v-for="tag in entrustedTags" :key="tag" class="text-[0.78rem] font-semibold text-gold-300/86">
              {{ tag }}
            </span>
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
const entrustedAssets = ['Time', 'Gifts', 'Opportunities', 'Knowledge', 'Influence', 'Resources', 'Experiences', 'Life'];
const entrustedTags = [
  '#Entrusted',
  '#EntrustedBook',
  '#FemiOwoyele',
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
    const authorizationUrl = response.meta.authorization_url;

    if (/^https?:\/\//i.test(authorizationUrl)) {
      window.location.assign(authorizationUrl);
      return;
    }

    router.push(authorizationUrl);
  } catch (caught) {
    serverError.value =
      caught.response?.data?.message || 'Your pre-order could not be started. Please try again shortly.';
  }
});

usePageMeta(() => ({
  title: `Pre-order ${book.value.title || 'Entrusted'}`,
  description: `Pre-order ${book.value.title || 'Entrusted'} by Femi Owoyele and receive collection details by email.`,
}));
</script>
