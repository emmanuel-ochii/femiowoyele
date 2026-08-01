import { defineStore } from 'pinia';

export const useSiteStore = defineStore('site', {
  state: () => ({
    navigation: [
      { label: 'About', to: '/about' },
      { label: 'Work', to: '/work' },
      { label: 'Build Tomorrow', to: '/build-tomorrow' },
      { label: 'Research & Ideas', to: '/research-ideas' },
      { label: 'Books', to: '/books' },
      { label: 'Speaking', to: '/speaking' },
      { label: 'Mentorship', to: '/mentorship' },
      { label: 'Impact', to: '/impact' },
      { label: 'Media', to: '/media' },
      { label: 'Journal', to: '/journal' },
      { label: 'Contact', to: '/contact' },
    ],
  }),
});
