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
            <!-- Stacked rather than justified: values such as a launch date are
                 too long to sit opposite their label without wrapping badly. -->
            <dl class="mt-8 space-y-5 border-t border-navy-900/12 pt-6 text-sm">
              <div>
                <dt class="text-micro font-semibold uppercase text-ink-faint">Author</dt>
                <dd class="mt-1.5 font-medium text-navy-900">Femi Owoyele</dd>
              </div>
              <div>
                <dt class="text-micro font-semibold uppercase text-ink-faint">Status</dt>
                <dd class="mt-1.5 font-medium text-navy-900">{{ status }}</dd>
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
import { formatDate } from '../../utils/format';

const route = useRoute();
const { payload, loading, error, load, retry } = useApiPage(() => `/books/${route.params.slug}`);

watch(() => route.params.slug, load);

const book = computed(() => payload.value?.data || {});
const launch = computed(() => payload.value?.meta?.launch || null);

/**
 * The book this launch event belongs to reports its launch date until the
 * evening has passed; everything else falls back to the featured flag.
 */
const status = computed(() => {
  const meta = launch.value?.meta;
  const isLaunchTitle = meta?.book_slug && meta.book_slug === book.value.slug;

  if (isLaunchTitle && meta.starts_at) {
    const startsAt = new Date(meta.starts_at).getTime();
    if (!Number.isNaN(startsAt) && startsAt > Date.now()) {
      return `Launching ${meta.date_label || formatDate(meta.starts_at)}`;
    }
    return 'Published';
  }

  return book.value.is_featured ? 'Published' : 'In progress';
});

usePageMeta(() => ({
  title: book.value.title || 'Book',
  description: book.value.subtitle || book.value.description,
}));
</script>
