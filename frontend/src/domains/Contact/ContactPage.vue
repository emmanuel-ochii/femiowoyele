<template>
  <PageHero
    eyebrow="Contact"
    title="Begin the right conversation."
    description="Use the form for speaking, consulting, research, partnership, media, mentorship, or general enquiries."
  />
  <SectionWrapper>
    <div class="grid gap-12 lg:grid-cols-[0.75fr_1.25fr]">
      <div>
        <p class="eyebrow">Enquiries</p>
        <h2 class="heading-md mt-4 text-navy">Clear context helps the work move well.</h2>
        <p class="prose-content mt-5">
          Share the purpose, audience, timeline, and any relevant institutional context. The CMS stores contact
          messages under the secure backend for follow-up.
        </p>
      </div>
      <ContactForm />
    </div>
  </SectionWrapper>
</template>

<script setup>
import ContactForm from '../../components/forms/ContactForm.vue';
import PageHero from '../../components/sections/PageHero.vue';
import SectionWrapper from '../../components/ui/SectionWrapper.vue';
</script>
