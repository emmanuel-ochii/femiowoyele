import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import { createRouter, createWebHistory } from 'vue-router';
import BookLaunchSection from '../components/sections/BookLaunchSection.vue';
import { reveal } from '../directives/reveal';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: { template: '<div />' } },
    { path: '/contact', component: { template: '<div />' } },
    { path: '/entrusted', component: { template: '<div />' } },
    { path: '/pre-order', component: { template: '<div />' } },
    { path: '/books/:slug', component: { template: '<div />' } },
  ],
});

const launchBlock = () => ({
  slug: 'home.launch',
  title: 'Entrusted',
  body: 'Entrusted brings together years of writing, teaching, mentoring, building, and service.',
  meta: {
    occasion: 'Stewardship. Responsibility. Purpose.',
    tagline: 'Stewardship. Responsibility. Purpose.',
    subtitle: 'Lessons, responsibilities and truths for a life of legacy',
    book_slug: 'entrusted',
  },
});

const mountSection = async (launch) => {
  await router.push('/');
  await router.isReady();

  return mount(BookLaunchSection, {
    props: { launch },
    global: { plugins: [router], directives: { reveal } },
  });
};

describe('BookLaunchSection', () => {
  it('renders nothing when no Entrusted block is available', async () => {
    const wrapper = await mountSection(null);
    expect(wrapper.find('#entrusted-book').exists()).toBe(false);
  });

  it('presents Entrusted without event logistics', async () => {
    const wrapper = await mountSection(launchBlock());
    const text = wrapper.text();

    expect(text).toContain('Featured book');
    expect(text).toContain('Entrusted');
    expect(text).toContain('Lessons, responsibilities and truths for a life of legacy');
    expect(text).toContain('Stewardship. Responsibility. Purpose.');
    expect(text).toContain('Pre-order Entrusted');
    expect(text).toContain('About the book');
    expect(wrapper.find('a[href="/pre-order"]').exists()).toBe(true);
    expect(wrapper.find('a[href="/books/entrusted"]').exists()).toBe(true);
    expect(text).not.toContain('Venue');
    expect(text).not.toContain('Address');
    expect(wrapper.find('a[href="/rsvp"]').exists()).toBe(false);
  });
});
