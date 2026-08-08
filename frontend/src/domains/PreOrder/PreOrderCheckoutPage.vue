<template>
  <section class="surface-navy on-dark relative min-h-screen overflow-hidden pt-header">
    <!--
      Stand-in for a hosted provider checkout. When Paystack is wired in, the
      gateway returns its own authorization_url and this route stops being used.
      The comment must sit inside the root element: a comment beside it gives the
      component two roots, which stalls the layout's <Transition mode="out-in">.
    -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute left-1/2 top-0 h-[38rem] w-[38rem] -translate-x-1/2 rounded-full bg-gold-500/10 blur-[130px]"></div>
    </div>

    <div class="shell relative py-14 lg:py-20">
      <LoadingState v-if="loading" label="Loading your order" class="!px-0" />
      <ErrorState v-else-if="error" :on-retry="retry" title="This order could not be found." />

      <div v-else class="mx-auto max-w-xl">
        <TestModeBanner message="This screen stands in for the payment provider. Nothing is charged." />

        <div class="on-light mt-8 border border-navy-900/10 bg-white p-7 shadow-frame sm:p-10">
          <p class="eyebrow">Confirm payment</p>
          <h1 class="display-4 mt-4 text-navy-900">{{ order.total_display }}</h1>
          <p class="body-copy mt-3">
            {{ order.quantity }} {{ order.quantity === 1 ? 'copy' : 'copies' }} of Entrusted, for
            {{ order.name }}.
          </p>

          <dl class="mt-8 space-y-4 border-t border-navy-900/10 pt-7 text-sm">
            <div class="flex justify-between gap-4">
              <dt class="text-ink-faint">Reference</dt>
              <dd class="font-medium tabular-nums text-navy-900">{{ order.reference }}</dd>
            </div>
            <div class="flex justify-between gap-4">
              <dt class="text-ink-faint">Email</dt>
              <dd class="font-medium text-navy-900">{{ order.email }}</dd>
            </div>
          </dl>

          <div v-if="alreadyPaid" class="mt-8">
            <p class="body-copy">This order has already been paid.</p>
            <BaseButton :to="`/pre-order/complete?reference=${reference}`" variant="primary" class="mt-6" icon="arrowRight">
              View your order
            </BaseButton>
          </div>

          <div v-else class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <BaseButton variant="primary" size="lg" :loading="working" icon="arrowRight" @click="pay">
              {{ working ? 'Processing' : 'Pay now' }}
            </BaseButton>
            <BaseButton variant="ghost" size="lg" :disabled="working" @click="cancel">Cancel payment</BaseButton>
          </div>

          <p v-if="failureMessage" class="field-error mt-5">{{ failureMessage }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import TestModeBanner from '../../components/sections/TestModeBanner.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';
import { contentApi } from '../../services/contentApi';

const route = useRoute();
const router = useRouter();
const reference = computed(() => route.query.reference || '');

const { payload, loading, error, retry } = useApiPage(() => `/pre-order/${reference.value}`);
const order = computed(() => payload.value?.data || {});
const alreadyPaid = computed(() => order.value.status === 'paid');

const working = ref(false);
const failureMessage = ref('');

/** Mirrors a provider redirect: hand off to the callback route, which verifies. */
const complete = () => router.push(`/pre-order/complete?reference=${reference.value}`);

const pay = async () => {
  working.value = true;
  failureMessage.value = '';
  try {
    complete();
  } finally {
    working.value = false;
  }
};

const cancel = async () => {
  working.value = true;
  failureMessage.value = '';

  try {
    await contentApi.post(`/pre-order/${reference.value}/simulate`, { cancel: true });
    complete();
  } catch {
    failureMessage.value = 'That could not be completed. Please try again.';
  } finally {
    working.value = false;
  }
};

usePageMeta({ title: 'Confirm payment', description: 'Confirm your pre-order payment.' });
</script>
