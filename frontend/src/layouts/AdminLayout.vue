<template>
  <div class="min-h-screen bg-mist text-ink">
    <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-navy/10 bg-white p-6 lg:block">
      <RouterLink to="/" class="font-serif text-2xl text-navy">Femi Owoyele</RouterLink>
      <p class="mt-2 text-xs font-semibold uppercase text-forest">Content Studio</p>
      <nav class="mt-10 grid gap-2">
        <RouterLink
          to="/admin"
          class="focus-ring px-3 py-2 text-sm font-semibold text-ink/70 hover:bg-sand"
          exact-active-class="bg-sand text-navy"
        >
          Dashboard
        </RouterLink>
        <RouterLink
          v-for="item in resources"
          :key="item.slug"
          :to="`/admin/content/${item.slug}`"
          class="focus-ring px-3 py-2 text-sm font-semibold text-ink/70 hover:bg-sand"
          active-class="bg-sand text-navy"
        >
          {{ item.label }}
        </RouterLink>
      </nav>
    </aside>

    <div class="lg:pl-64">
      <header class="sticky top-0 z-30 border-b border-navy/10 bg-white/92 px-5 py-4 backdrop-blur sm:px-8">
        <div class="flex items-center justify-between gap-4">
          <RouterLink to="/" class="font-serif text-xl text-navy lg:hidden">Femi Owoyele</RouterLink>
          <p class="hidden text-sm font-semibold text-ink/65 lg:block">Admin CMS</p>
          <button class="focus-ring text-sm font-semibold text-forest" type="button" @click="logout">Logout</button>
        </div>
      </header>
      <main class="px-5 py-8 sm:px-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { adminResources } from '../domains/Admin/adminResources';
import { useAuthStore } from '../stores/auth';

const resources = adminResources;
const auth = useAuthStore();
const router = useRouter();

const logout = async () => {
  await auth.logout();
  router.push('/admin/login');
};
</script>
