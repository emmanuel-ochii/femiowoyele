<template>
  <section class="surface-navy on-dark relative min-h-screen overflow-hidden pt-header">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -left-40 top-0 h-[40rem] w-[40rem] rounded-full bg-gold-500/12 blur-[130px]"></div>
      <div class="absolute -right-40 bottom-0 h-[32rem] w-[32rem] rounded-full bg-gold-400/10 blur-[120px]"></div>
    </div>

    <div class="shell relative py-14 lg:py-20">
      <LoadingState v-if="verifying" label="Confirming your payment" class="!px-0" />
      <ErrorState
        v-else-if="error"
        :on-retry="verify"
        title="We could not confirm this payment."
        message="Your card may not have been charged. Try again, or contact us with your reference."
      />

      <!-- ------------------------------------------------------ succeeded -->
      <div v-else-if="isPaid">
        <div v-reveal class="mx-auto max-w-2xl text-center">
          <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-gold-400/60 text-gold-400">
            <AppIcon name="check" :size="26" />
          </span>
          <p class="eyebrow mt-8 justify-center !text-gold-400">Pre-order confirmed</p>
          <h1 class="display-2 mt-5 text-balance text-white">Thank you, {{ firstName }}.</h1>
          <p class="lead mt-6 text-pretty !text-white/75">
            Your {{ order.quantity === 1 ? 'copy' : `${order.quantity} copies` }} of
            <em class="not-italic text-gold-300">Entrusted</em> {{ order.quantity === 1 ? 'is' : 'are' }} reserved. A
            receipt is on its way to {{ order.email }}.
          </p>
        </div>

        <!-- Order summary -->
        <div v-reveal="80" class="mx-auto mt-12 max-w-2xl border-y border-white/15">
          <dl class="grid grid-cols-2 divide-x divide-white/15 sm:grid-cols-4">
            <div v-for="item in summary" :key="item.label" class="px-5 py-6 first:pl-0">
              <dt class="text-micro font-semibold uppercase text-gold-400">{{ item.label }}</dt>
              <dd class="mt-2.5 font-serif text-lg tabular-nums text-white">{{ item.value }}</dd>
            </div>
          </dl>
        </div>

        <!-- ------------------------------------------------ pickup points -->
        <div v-reveal="140" class="mx-auto mt-16 max-w-4xl">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="eyebrow !text-gold-400">Collection</p>
              <h2 class="display-3 mt-5 text-balance text-white">Where to collect your book</h2>
            </div>
            <p class="text-[0.85rem] leading-6 text-white/55 sm:max-w-xs sm:text-right">
              Quote reference <span class="font-medium text-white">{{ order.reference }}</span> at any point below.
            </p>
          </div>

          <div v-if="pickupPoints.length" class="mt-10 overflow-x-auto">
            <table class="w-full min-w-[38rem] border-collapse text-left">
              <caption class="sr-only">Pickup points for collecting your pre-ordered copy</caption>
              <thead>
                <tr class="border-y border-white/15">
                  <th v-for="head in tableHeads" :key="head" scope="col" class="px-4 py-4 text-micro font-semibold uppercase text-gold-400 first:pl-0">
                    {{ head }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="point in pickupPoints" :key="point.id" class="border-b border-white/10 align-top">
                  <td class="px-4 py-6 pl-0">
                    <p class="font-serif text-lg leading-snug text-white">{{ point.name }}</p>
                    <p v-if="point.note" class="mt-1.5 text-[0.82rem] leading-6 text-white/50">{{ point.note }}</p>
                  </td>
                  <td class="px-4 py-6 text-[0.9rem] leading-6 text-white/75">
                    {{ point.address }}<template v-if="point.city">, {{ point.city }}</template>
                  </td>
                  <td class="px-4 py-6 text-[0.9rem] leading-6 text-white/75">{{ point.opening_hours || '—' }}</td>
                  <td class="px-4 py-6 text-[0.9rem] leading-6 text-white/75">
                    <a v-if="point.contact_phone" :href="`tel:${point.contact_phone.replace(/\s+/g, '')}`" class="transition-colors hover:text-gold-300">
                      {{ point.contact_phone }}
                    </a>
                    <span v-else>—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <p v-else class="mt-10 border border-dashed border-white/20 px-6 py-10 text-center text-[0.9rem] text-white/60">
            Collection points are being confirmed. We will email you as soon as they are published.
          </p>

          <div class="mt-12 flex flex-wrap gap-x-4 gap-y-3">
            <BaseButton to="/entrusted" variant="gold" size="lg" icon="arrowRight">About Entrusted</BaseButton>
            <BaseButton to="/books/entrusted" variant="outline-light" size="lg">About the book</BaseButton>
          </div>
        </div>
      </div>

      <!-- --------------------------------------------------- not completed -->
      <div v-else class="mx-auto max-w-xl">
        <div class="on-light border border-navy-900/10 bg-white p-7 shadow-frame sm:p-10">
          <p class="eyebrow">Payment not completed</p>
          <h1 class="display-4 mt-4 text-navy-900">Your order is still waiting.</h1>
          <p class="body-copy mt-4">
            {{ message || 'The payment was not completed, so nothing has been charged. Your reserved copy is held under reference ' + order.reference + '.' }}
          </p>
          <div class="mt-8 flex flex-wrap gap-x-4 gap-y-3">
            <BaseButton v-if="usesMockCheckout" :to="`/pre-order/checkout?reference=${reference}`" variant="primary" icon="arrowRight">
              Try payment again
            </BaseButton>
            <BaseButton v-else to="/pre-order" variant="primary" icon="arrowRight">Start a new payment</BaseButton>
            <BaseButton v-if="usesMockCheckout" to="/pre-order" variant="ghost">Start over</BaseButton>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import AppIcon from '../../components/ui/AppIcon.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import { usePageMeta } from '../../composables/usePageMeta';
import { contentApi } from '../../services/contentApi';

const route = useRoute();
const reference = computed(() => route.query.reference || '');

const order = ref({});
const pickupPoints = ref([]);
const message = ref('');
const verifying = ref(true);
const error = ref(null);

const isPaid = computed(() => order.value.status === 'paid');
const firstName = computed(() => String(order.value.name || '').split(' ')[0] || 'friend');
const usesMockCheckout = computed(() => order.value.payment_provider === 'mock');

const tableHeads = ['Pickup point', 'Address', 'Opening hours', 'Contact'];

const summary = computed(() => [
  { label: 'Reference', value: order.value.reference },
  { label: 'Copies', value: order.value.quantity },
  { label: 'Total paid', value: order.value.total_display },
  { label: 'Status', value: 'Paid' },
]);

/**
 * The callback verifies with the server rather than trusting the redirect.
 * This is the same shape Paystack expects: it returns the buyer here with a
 * reference, and the status is only ever settled server-side.
 */
const verify = async () => {
  verifying.value = true;
  error.value = null;

  try {
    const response = await contentApi.post(`/pre-order/${reference.value}/verify`);
    order.value = response.data;
    pickupPoints.value = response.meta?.pickup_points || [];
    message.value = response.meta?.message || '';
  } catch (caught) {
    error.value = caught;
  } finally {
    verifying.value = false;
  }
};

onMounted(verify);

usePageMeta({ title: 'Pre-order confirmed', description: 'Your pre-order of Entrusted is confirmed.' });
</script>
