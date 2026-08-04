<template>
  <LoadingState v-if="loading" />
  <ErrorState
    v-else-if="error"
    :on-retry="retry"
    title="This book could not be loaded."
    message="It may have moved, or the connection was interrupted. Try again, or browse the full catalogue."
  />

  <div v-else>
    <PageHero
      :breadcrumb="{ label: 'Books', to: '/books' }"
      :eyebrow="book.is_featured ? 'Featured work' : 'Book'"
      :title="book.title"
      :description="book.subtitle"
    >
      <template #actions>
        <BaseButton to="/contact" variant="gold" icon="arrowRight">Enquire about this book</BaseButton>
        <BaseButton to="/books" variant="outline-light">All books</BaseButton>
      </template>
    </PageHero>

    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="mx-auto w-full max-w-[260px] lg:col-span-4 lg:mx-0 lg:max-w-[300px]">
          <div class="lg:sticky lg:top-32">
            <BookCover :book="book" />
            <dl class="mt-8 space-y-4 border-t border-navy-900/12 pt-6 text-sm">
              <div class="flex justify-between gap-4">
                <dt class="text-ink-faint">Author</dt>
                <dd class="font-medium text-navy-900">Femi Owoyele</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-ink-faint">Status</dt>
                <dd class="font-medium text-navy-900">{{ book.is_featured ? 'Published' : 'In progress' }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-reveal="80" class="lg:col-span-8">
          <p class="eyebrow">About the book</p>
          <ProseBody :body="book.description" class="mt-6 !max-w-none" />

          <div class="mt-14 border-t border-navy-900/12 pt-8">
            <h2 class="display-4 text-navy-900">Enquiries</h2>
            <p class="body-copy mt-4 max-w-xl">
              For interviews, review copies, festival appearances, bulk orders, or translation rights, send a note with
              the details and timeline.
            </p>
            <BaseButton to="/contact" variant="primary" class="mt-7" icon="arrowRight">Start an enquiry</BaseButton>
          </div>
        </div>
      </div>
    </SectionWrapper>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import BookCover from '../../components/cards/BookCover.vue';
import PageHero from '../../components/sections/PageHero.vue';
import ProseBody from '../../components/sections/ProseBody.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import ErrorState from '../../components/ui/ErrorState.vue';
import LoadingState from '../../components/ui/LoadingState.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { useApiPage } from '../../composables/useApiPage';
import { usePageMeta } from '../../composables/usePageMeta';

const route = useRoute();
const { payload, loading, error, load, retry } = useApiPage(() => `/books/${route.params.slug}`);

watch(() => route.params.slug, load);

const book = computed(() => payload.value?.data || {});

usePageMeta(() => ({
  title: book.value.title || 'Book',
  description: book.value.subtitle || book.value.description,
}));
</script>
