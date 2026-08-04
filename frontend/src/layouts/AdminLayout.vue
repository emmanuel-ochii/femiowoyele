<template>
  <div class="min-h-screen bg-sand-50 text-ink">
    <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-navy-900/10 bg-white p-7 lg:block">
      <RouterLink to="/" class="font-serif text-xl text-navy-900">Femi Owoyele</RouterLink>
      <p class="mt-2 text-micro font-semibold uppercase text-forest-700">Content studio</p>

      <nav class="mt-10 grid gap-0.5" aria-label="Admin sections">
        <RouterLink
          to="/admin"
          class="px-3 py-2.5 text-[0.85rem] font-medium text-ink-muted transition-colors hover:bg-sand-100 hover:text-navy-900"
          exact-active-class="!bg-navy-900 !text-white"
        >
          Dashboard
        </RouterLink>
        <RouterLink
          v-for="item in resources"
          :key="item.slug"
          :to="`/admin/content/${item.slug}`"
          class="px-3 py-2.5 text-[0.85rem] font-medium text-ink-muted transition-colors hover:bg-sand-100 hover:text-navy-900"
          active-class="!bg-navy-900 !text-white"
        >
          {{ item.label }}
        </RouterLink>
      </nav>
    </aside>

    <div class="lg:pl-64">
      <header class="sticky top-0 z-30 border-b border-navy-900/10 bg-white/94 backdrop-blur">
        <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
          <RouterLink to="/" class="font-serif text-lg text-navy-900 lg:hidden">Femi Owoyele</RouterLink>
          <p class="hidden text-micro font-semibold uppercase text-ink-faint lg:block">Content studio</p>
          <div class="flex items-center gap-6">
            <RouterLink to="/" class="text-[0.82rem] font-medium text-ink-muted transition-colors hover:text-navy-900">
              View site
            </RouterLink>
            <button
              class="text-[0.82rem] font-semibold text-forest-800 transition-colors hover:text-forest-600"
              type="button"
              @click="logout"
            >
              Sign out
            </button>
          </div>
        </div>
      </header>

      <main class="px-5 py-10 sm:px-8">
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
