<template>
  <div class="flex min-h-screen items-center justify-center bg-sand px-5 py-12">
    <form class="w-full max-w-md bg-white p-8 shadow-soft" @submit.prevent="submit">
      <RouterLink to="/" class="font-serif text-2xl text-navy">Femi Owoyele</RouterLink>
      <p class="mt-2 text-sm font-semibold text-forest">Admin CMS</p>
      <label class="mt-8 grid gap-2 text-sm font-semibold text-navy">
        Email
        <input v-model="email" class="focus-ring border border-navy/15 px-4 py-3 font-normal" type="email" />
      </label>
      <label class="mt-5 grid gap-2 text-sm font-semibold text-navy">
        Password
        <input v-model="password" class="focus-ring border border-navy/15 px-4 py-3 font-normal" type="password" />
      </label>
      <p v-if="error" class="mt-4 text-sm text-red-700">{{ error }}</p>
      <BaseButton class="mt-6 w-full" type="submit">Sign in</BaseButton>
      <p class="mt-5 text-xs leading-5 text-ink/55">Seeded local admin: admin@femiowoyele.com / password</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import BaseButton from '../../components/ui/BaseButton.vue';
import { useAuthStore } from '../../stores/auth';

const email = ref('admin@femiowoyele.com');
const password = ref('password');
const error = ref('');
const auth = useAuthStore();
const router = useRouter();

const submit = async () => {
  error.value = '';

  try {
    await auth.login({ email: email.value, password: password.value });
    router.push('/admin');
  } catch (caught) {
    error.value = caught.response?.data?.message || 'Unable to sign in.';
  }
};
</script>
