<template>
  <header
    :class="[
      'fixed inset-x-0 top-0 z-50 transition-[background-color,border-color,box-shadow] duration-300 ease-editorial',
      isSolid
        ? 'border-b border-navy-900/10 bg-white/92 backdrop-blur-md'
        : 'border-b border-white/12 bg-transparent on-dark',
    ]"
  >
    <div class="shell flex h-header items-center justify-between gap-8">
      <RouterLink
        to="/"
        :class="['shrink-0 font-serif text-[1.35rem] tracking-[-0.01em] transition-colors', isSolid ? 'text-navy-900' : 'text-white']"
        aria-label="Femi Owoyele — home"
      >
        Femi Owoyele
      </RouterLink>

      <!-- ------------------------------------------------------- desktop -->
      <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary">
        <div
          v-for="group in navGroups"
          :key="group.label"
          class="relative"
          @mouseenter="group.items.length && openMenu(group.label)"
          @mouseleave="scheduleClose"
          @focusout="onGroupFocusOut($event, group.label)"
        >
          <!-- The trigger is a link, not a button: hovering or focusing it opens
               the panel, while activating it goes to the section overview. That
               keeps pointer and keyboard behaviour consistent. -->
          <RouterLink
            :id="`navbtn-${slug(group.label)}`"
            :to="group.to"
            :aria-expanded="group.items.length ? menuOpen === group.label : undefined"
            :aria-controls="group.items.length ? `navpanel-${slug(group.label)}` : undefined"
            :class="[navItemClass, groupToneClass(group)]"
            @focus="group.items.length && openMenu(group.label)"
            @keydown.escape.prevent="closeMenu(true)"
          >
            {{ group.label }}
            <AppIcon
              v-if="group.items.length"
              name="chevronDown"
              :size="13"
              :class="['transition-transform duration-200', menuOpen === group.label && 'rotate-180']"
            />
          </RouterLink>

          <Transition
            enter-active-class="transition duration-200 ease-editorial"
            enter-from-class="opacity-0 -translate-y-1"
            leave-active-class="transition duration-150"
            leave-to-class="opacity-0"
          >
            <div
              v-if="menuOpen === group.label"
              :id="`navpanel-${slug(group.label)}`"
              class="absolute left-0 top-full w-[22rem] border border-navy-900/10 border-t-2 border-t-gold-500 bg-white p-2 shadow-lift"
              @mouseenter="cancelClose"
            >
              <p class="mx-4 border-b border-navy-900/8 pb-4 pt-4 text-[0.78rem] leading-6 text-ink-faint">
                {{ group.summary }}
              </p>
              <RouterLink
                v-for="item in group.items"
                :key="item.to"
                :to="item.to"
                class="group/item block px-4 py-3 transition-colors duration-200 hover:bg-sand-100"
                @click="closeMenu()"
              >
                <span class="flex items-center gap-2 font-serif text-[1.05rem] text-navy-900">
                  {{ item.label }}
                  <AppIcon
                    name="arrowRight"
                    :size="14"
                    class="text-gold-600 opacity-0 transition-all duration-200 group-hover/item:translate-x-0.5 group-hover/item:opacity-100"
                  />
                </span>
                <span class="mt-1 block text-[0.8rem] leading-6 text-ink-faint">{{ item.description }}</span>
              </RouterLink>
            </div>
          </Transition>
        </div>
      </nav>

      <div class="hidden lg:block">
        <BaseButton to="/contact" :variant="isSolid ? 'outline' : 'outline-light'" size="sm" icon="arrowRight">
          Start a conversation
        </BaseButton>
      </div>

      <!-- -------------------------------------------------------- mobile -->
      <button
        ref="toggleRef"
        type="button"
        :class="[
          'inline-flex h-11 w-11 shrink-0 items-center justify-center border transition-colors lg:hidden',
          isSolid ? 'border-navy-900/20 text-navy-900' : 'border-white/30 text-white',
        ]"
        :aria-expanded="drawerOpen"
        aria-controls="mobile-nav"
        :aria-label="drawerOpen ? 'Close navigation' : 'Open navigation'"
        @click="drawerOpen = !drawerOpen"
      >
        <AppIcon :name="drawerOpen ? 'close' : 'menu'" :size="20" />
      </button>
    </div>
  </header>

  <!-- Mobile drawer lives outside the header so it can own the whole screen. -->
  <Transition
    enter-active-class="transition duration-300 ease-editorial"
    enter-from-class="opacity-0"
    leave-active-class="transition duration-200"
    leave-to-class="opacity-0"
  >
    <div
      v-if="drawerOpen"
      id="mobile-nav"
      ref="drawerRef"
      class="surface-navy on-dark fixed inset-0 z-40 overflow-y-auto pt-header lg:hidden"
      role="dialog"
      aria-modal="true"
      aria-label="Site navigation"
      @keydown.escape.prevent="drawerOpen = false"
      @keydown.tab="trapFocus"
    >
      <div class="shell pb-16 pt-8">
        <p class="eyebrow !text-gold-400">Navigate</p>
        <nav class="mt-6" aria-label="Mobile primary">
          <div v-for="group in navGroups" :key="group.label" class="border-t border-white/12 py-6">
            <p v-if="group.items.length" class="text-micro font-semibold uppercase text-white/40">{{ group.label }}</p>
            <RouterLink
              v-for="item in group.items.length ? group.items : [group]"
              :key="item.to"
              :to="item.to"
              class="mt-4 flex items-baseline justify-between gap-4 font-serif text-2xl text-white transition-colors first:mt-0 hover:text-gold-300"
              @click="drawerOpen = false"
            >
              {{ item.label }}
              <AppIcon name="arrowRight" :size="16" class="shrink-0 translate-y-[-2px] text-gold-500" />
            </RouterLink>
          </div>
        </nav>

        <div class="mt-10">
          <BaseButton to="/contact" variant="gold" size="lg" icon="arrowRight" class="w-full" @click="drawerOpen = false">
            Start a conversation
          </BaseButton>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useSiteStore } from '../../stores/site';
import AppIcon from '../ui/AppIcon.vue';
import BaseButton from '../ui/BaseButton.vue';

const route = useRoute();
const site = useSiteStore();
const navGroups = computed(() => site.navGroups);

const scrolled = ref(false);
const menuOpen = ref(null);
const drawerOpen = ref(false);
const drawerRef = ref(null);
const toggleRef = ref(null);
let closeTimer = null;

/**
 * The header only stays transparent over a dark hero, at the top of the page,
 * and while nothing is overlaid on it — an open dropdown or drawer needs the
 * solid surface so its own text stays legible.
 */
const isSolid = computed(
  () => !route.meta.transparentHeader || scrolled.value || drawerOpen.value || Boolean(menuOpen.value),
);

const navItemClass = 'flex items-center gap-1.5 px-4 py-2.5 text-[0.85rem] font-semibold transition-colors duration-200';

const slug = (value) => value.toLowerCase().replace(/[^a-z]+/g, '-');

const matches = (path) => route.path === path || route.path.startsWith(`${path}/`);
const isGroupActive = (group) => (group.items.length ? group.items.some((item) => matches(item.to)) : matches(group.to));

const groupToneClass = (group) => {
  if (isGroupActive(group)) return isSolid.value ? 'text-forest-800' : 'text-gold-300';
  return isSolid.value ? 'text-ink-muted hover:text-navy-900' : 'text-white/75 hover:text-white';
};

const onScroll = () => {
  scrolled.value = window.scrollY > 24;
};

const cancelClose = () => {
  clearTimeout(closeTimer);
};

const openMenu = (label) => {
  cancelClose();
  menuOpen.value = label;
};

const closeMenu = (returnFocus = false) => {
  cancelClose();
  const label = menuOpen.value;
  menuOpen.value = null;

  if (returnFocus && label) {
    nextTick(() => document.getElementById(`navbtn-${slug(label)}`)?.focus());
  }
};

// A short grace period keeps the panel from vanishing while the pointer
// travels from the trigger down into it.
const scheduleClose = () => {
  cancelClose();
  closeTimer = setTimeout(() => {
    menuOpen.value = null;
  }, 160);
};

const onGroupFocusOut = (event, label) => {
  if (menuOpen.value !== label) return;
  if (!event.currentTarget.contains(event.relatedTarget)) menuOpen.value = null;
};

/** Keeps Tab cycling inside the mobile drawer while it is open. */
const trapFocus = (event) => {
  const focusables = drawerRef.value?.querySelectorAll('a[href], button:not([disabled])');
  if (!focusables?.length) return;

  const first = focusables[0];
  const last = focusables[focusables.length - 1];

  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
};

watch(drawerOpen, (open) => {
  document.body.style.overflow = open ? 'hidden' : '';
  if (open) nextTick(() => drawerRef.value?.querySelector('a[href]')?.focus());
  else toggleRef.value?.focus();
});

watch(
  () => route.fullPath,
  () => {
    drawerOpen.value = false;
    menuOpen.value = null;
  },
);

onMounted(() => {
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll);
  clearTimeout(closeTimer);
  document.body.style.overflow = '';
});
</script>
