<template>
  <div class="flex min-h-screen flex-col bg-white text-ink">
    <a
      href="#main"
      class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:bg-navy-900 focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-white"
    >
      Skip to content
    </a>

    <SiteHeader />

    <main id="main" class="flex-1">
      <RouterView v-slot="{ Component, route }">
        <Transition
          mode="out-in"
          enter-active-class="transition-opacity duration-300 ease-editorial"
          enter-from-class="opacity-0"
          leave-active-class="transition-opacity duration-150"
          leave-to-class="opacity-0"
        >
          <component :is="Component" :key="route.path" />
        </Transition>
      </RouterView>
    </main>

    <SiteFooter />
  </div>
</template>

<script setup>
import SiteFooter from '../components/layout/SiteFooter.vue';
import SiteHeader from '../components/layout/SiteHeader.vue';
</script>
