import { defineStore } from 'pinia';

/**
 * Site navigation.
 *
 * Eleven top-level destinations is too many for a single bar, so the primary
 * nav collapses into four groups. `navigation` stays as a flat list for the
 * footer, sitemap, and mobile drawer.
 */
export const useSiteStore = defineStore('site', {
  state: () => ({
    navigation: [
      { label: 'About', to: '/about' },
      { label: 'Work', to: '/work' },
      { label: 'Build Tomorrow', to: '/build-tomorrow' },
      { label: 'Research & Ideas', to: '/research-ideas' },
      { label: 'Books', to: '/books' },
      { label: 'Entrusted', to: '/entrusted' },
      { label: 'Speaking', to: '/speaking' },
      { label: 'Mentorship', to: '/mentorship' },
      { label: 'Impact', to: '/impact' },
      { label: 'Media', to: '/media' },
      { label: 'Journal', to: '/journal' },
      { label: 'Contact', to: '/contact' },
    ],

    navGroups: [
      {
        label: 'About',
        to: '/about',
        summary: 'The person, the convictions, and the record behind the work.',
        items: [
          { label: 'About', to: '/about', description: 'Who I am and what I am building toward' },
          { label: 'Impact', to: '/impact', description: 'An evolving record of contribution' },
          { label: 'Media', to: '/media', description: 'Interviews, talks, and public record' },
        ],
      },
      {
        label: 'Work',
        to: '/work',
        summary: 'Six pillars of practice, and the platform built around them.',
        items: [
          { label: 'My Work', to: '/work', description: 'Enterprise, leadership, governance, and more' },
          { label: 'Build Tomorrow', to: '/build-tomorrow', description: 'A platform for emerging builders' },
          { label: 'Mentorship', to: '/mentorship', description: 'Building Builders programmes and resources' },
        ],
      },
      {
        label: 'Writing',
        to: '/research-ideas',
        summary: 'Essays, frameworks, books, and shorter reflections.',
        items: [
          { label: 'Research & Ideas', to: '/research-ideas', description: 'Long-form essays and frameworks' },
          { label: 'Books', to: '/books', description: 'Entrusted and works in progress' },
          { label: 'The Entrusted launch', to: '/entrusted', description: '18 August 2026, Ikeja, Lagos' },
          { label: 'Journal', to: '/journal', description: 'Shorter notes between publications' },
        ],
      },
      // No `items` renders a direct link rather than a dropdown.
      { label: 'Speaking', to: '/speaking', items: [] },
    ],

    footerGroups: [
      {
        label: 'The work',
        items: [
          { label: 'About', to: '/about' },
          { label: 'My Work', to: '/work' },
          { label: 'Build Tomorrow', to: '/build-tomorrow' },
          { label: 'Impact', to: '/impact' },
        ],
      },
      {
        label: 'Writing',
        items: [
          { label: 'Research & Ideas', to: '/research-ideas' },
          { label: 'Books', to: '/books' },
          { label: 'The Entrusted launch', to: '/entrusted' },
          { label: 'Journal', to: '/journal' },
        ],
      },
      {
        label: 'Engage',
        items: [
          { label: 'Speaking', to: '/speaking' },
          { label: 'Mentorship', to: '/mentorship' },
          { label: 'Media', to: '/media' },
          { label: 'Contact', to: '/contact' },
        ],
      },
    ],
  }),
});
