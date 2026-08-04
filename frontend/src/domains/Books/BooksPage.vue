<template>
  <LoadingState v-if="loading" />
  <ErrorState v-else-if="error" :on-retry="retry" />

  <div v-else>
    <PageHero
      eyebrow="Books"
      title="Writing that takes the long view."
      description="Books are where an argument is allowed to be patient. These are the works published and in progress."
    />

    <!-- ====================================================== book launch -->
    <BookLaunchSection :launch="launch" />

    <!-- =================================================== featured title -->
    <SectionWrapper v-if="featured && !launch" tone="paper">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-20">
        <div v-reveal class="mx-auto w-full max-w-[280px] lg:col-span-4 lg:mx-0 lg:max-w-[320px]">
          <BookCover :book="featured" />
        </div>
        <div v-reveal="80" class="lg:col-span-8">
          <p class="eyebrow">Featured work</p>
          <h2 class="display-2 mt-6 text-balance text-navy-900">{{ featured.title }}</h2>
          <p v-if="featured.subtitle" class="mt-4 font-serif text-xl text-forest-800">{{ featured.subtitle }}</p>
          <p class="body-copy mt-6 max-w-2xl">{{ featured.description }}</p>
          <div class="mt-9 flex flex-wrap gap-x-4 gap-y-3">
            <BaseButton :to="`/books/${featured.slug}`" variant="primary" size="lg" icon="arrowRight">
              About the book
            </BaseButton>
            <BaseButton to="/contact" variant="outline" size="lg">Enquire about rights or bulk orders</BaseButton>
          </div>
        </div>
      </div>
    </SectionWrapper>

    <!-- ====================================================== other works -->
    <SectionWrapper v-if="others.length">
      <SectionHeading :eyebrow="launch ? 'The catalogue' : 'Also in the catalogue'" :title="launch ? 'All works' : 'Further works'" />
      <div class="mt-12 grid gap-6">
        <BookCard v-for="(book, index) in others" :key="book.slug" v-reveal="index * 60" :book="book" />
      </div>
    </SectionWrapper>

    <EmptyState v-if="!books.length" class="mx-auto my-20 max-w-3xl" title="The catalogue is being prepared." />

    <CtaBand
      eyebrow="Enquiries"
      title="Speaking, interviews, and review copies."
      description="For press, festivals, bulk orders, or translation rights, start here."
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import BookCard from '../../components/cards/BookCard.vue';
import BookCover from '../../components/cards/BookCover.vue';
import BookLaunchSection from '../../components/sections/BookLaunchSection.vue';
import CtaBand from '../../components/sections/CtaBand.vue';
import PageHero from '../../components/sections/PageHero.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionHeading from '../../components/ui/SectionHeading.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const { payload, loading, error, retry } = useApiPage('/books');
const books = computed(() => payload.value?.data || []);
const launch = computed(() => {
  const block = payload.value?.meta?.launch;
  return block?.slug ? block : null;
});
const featured = computed(() => books.value.find((book) => book.is_featured) || books.value[0] || null);
const others = computed(() =>
  launch.value ? books.value : books.value.filter((book) => book.slug !== featured.value?.slug),
);

usePageMeta({
  title: 'Books',
  description: 'Books by Femi Owoyele, including Entrusted — on leadership, stewardship, and the work of responsibility.',
});
</script>
