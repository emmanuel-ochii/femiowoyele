<template>
  <PageHero eyebrow="404" title="This page is not available." description="The requested route does not exist." />
  <SectionWrapper>
    <BaseButton to="/">Return home</BaseButton>
  </SectionWrapper>
</template>

<script setup>
import PageHero from '../components/sections/PageHero.vue';
import BaseButton from '../components/ui/BaseButton.vue';
import SectionWrapper from '../components/ui/SectionWrapper.vue';
</script>
