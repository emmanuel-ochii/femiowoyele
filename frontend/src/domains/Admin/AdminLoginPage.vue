<template>
  <div class="surface-navy on-dark flex min-h-screen items-center justify-center px-5 py-12">
    <form class="on-light w-full max-w-md border border-navy-900/10 bg-white p-8 shadow-frame sm:p-10" @submit.prevent="submit">
      <RouterLink to="/" class="font-serif text-2xl text-navy-900">Femi Owoyele</RouterLink>
      <p class="mt-2 text-micro font-semibold uppercase text-forest-700">Content studio</p>

      <div class="mt-10 grid gap-7">
        <div>
          <label class="field-label" for="admin-email">Email</label>
          <input
            id="admin-email"
            v-model="email"
            class="field-input mt-2"
            type="email"
            autocomplete="username"
            required
            autofocus
          />
        </div>
        <div>
          <label class="field-label" for="admin-password">Password</label>
          <input
            id="admin-password"
            v-model="password"
            class="field-input mt-2"
            type="password"
            autocomplete="current-password"
            required
          />
        </div>
      </div>

      <p v-if="error" class="field-error" role="alert">{{ error }}</p>

      <BaseButton class="mt-8 w-full" type="submit" size="lg" :loading="submitting" icon="arrowRight">
        {{ submitting ? 'Signing in' : 'Sign in' }}
      </BaseButton>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import BaseButton from '../../components/ui/BaseButton.vue';
import { useAuthStore } from '../../stores/auth';

const email = ref('');
const password = ref('');
const error = ref('');
const submitting = ref(false);
const auth = useAuthStore();
const router = useRouter();

const submit = async () => {
  error.value = '';
  submitting.value = true;

  try {
    await auth.login({ email: email.value, password: password.value });
    router.push('/admin');
  } catch (caught) {
    error.value = caught.response?.data?.message || 'Those details were not recognised.';
  } finally {
    submitting.value = false;
  }
};
</script>
