<template>
  <div>
    <PageHero
      eyebrow="Contact"
      title="Begin the right conversation."
      description="Speaking invitations, advisory work, mentorship, research collaborations, partnerships, and press — all start here."
    />

    <SectionWrapper>
      <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-32">
            <p class="eyebrow">What to include</p>
            <h2 class="display-3 mt-6 text-balance text-navy-900">Context makes a reply useful.</h2>
            <p class="body-copy mt-5">
              A few specifics — the audience, the timeline, and the outcome you have in mind — make it possible to give
              a straight answer rather than a holding note.
            </p>

            <dl class="mt-12 space-y-8 border-t border-navy-900/12 pt-8">
              <div v-for="route in enquiryRoutes" :key="route.title">
                <dt class="font-serif text-lg text-navy-900">{{ route.title }}</dt>
                <dd class="mt-2 text-[0.9rem] leading-7 text-ink-muted">{{ route.description }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-reveal="80" class="lg:col-span-8">
          <div class="border border-navy-900/10 bg-white p-7 shadow-soft sm:p-10">
            <ContactForm />
          </div>
        </div>
      </div>
    </SectionWrapper>

    <section class="surface-navy on-dark">
      <div class="shell py-16 lg:py-20">
        <div v-reveal class="grid gap-10 lg:grid-cols-12 lg:items-center lg:gap-20">
          <div class="lg:col-span-6">
            <p class="eyebrow !text-gold-400">Stay in touch</p>
            <h2 class="display-3 mt-6 text-balance text-white">Occasional notes, no noise.</h2>
          </div>
          <div class="lg:col-span-6">
            <p class="text-[0.95rem] leading-7 text-white/65">
              New essays, book news, and mentorship openings — sent only when there is something worth your attention.
            </p>
            <div class="mt-8">
              <NewsletterForm source="contact" />
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import ContactForm from '../../components/forms/ContactForm.vue';
import NewsletterForm from '../../components/forms/NewsletterForm.vue';
import PageHero from '../../components/sections/PageHero.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
import { usePageMeta } from '../../composables/usePageMeta';

const enquiryRoutes = [
  {
    title: 'Speaking and events',
    description: 'Keynotes, lectures, panels, and closed leadership sessions. Include the date, city, and format.',
  },
  {
    title: 'Mentorship',
    description: 'Applications for Building Builders cohorts and one-to-one mentorship conversations.',
  },
  {
    title: 'Advisory and partnership',
    description: 'Board work, institutional advisory, research collaborations, and platform partnerships.',
  },
  {
    title: 'Press and media',
    description: 'Interviews, review copies, biography, and photography requests.',
  },
];

usePageMeta({
  title: 'Contact',
  description:
    'Speaking invitations, advisory work, mentorship applications, research collaborations, partnerships, and press enquiries.',
});
</script>
