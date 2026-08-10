import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
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

const launchBlock = (startsAt) => ({
  slug: 'home.launch',
  title: 'Entrusted',
  body: 'Femi Owoyele will unveil his first book, Entrusted, in an intimate evening shaped around stewardship.',
  meta: {
    occasion: 'The Entrusted Launch',
    subtitle: 'Lessons, responsibilities and truths for a life of legacy',
    starts_at: startsAt,
    date_label: 'Tuesday, 18 August 2026',
    time_label: '4:00 p.m.',
    venue: 'Watercress Event Centre',
    address: '5 Alade Avenue, Allen, Ikeja, Lagos',
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
  beforeEach(() => {
    vi.useFakeTimers();
    vi.setSystemTime(new Date('2026-08-04T12:00:00+01:00'));
  });

  afterEach(() => vi.useRealTimers());

  it('renders nothing when no launch is scheduled', async () => {
    const wrapper = await mountSection(null);
    expect(wrapper.find('#launch').exists()).toBe(false);
  });

  it('counts down to an upcoming launch and shows the event details', async () => {
    const wrapper = await mountSection(launchBlock('2026-08-18T16:00:00+01:00'));
    const text = wrapper.text();

    expect(text).toContain('The unveiling');
    expect(text).toContain('Entrusted');
    expect(text).toContain('Lessons, responsibilities and truths for a life of legacy');
    expect(text).toContain('Counting down');
    expect(text).toContain('Watercress Event Centre');

    // 4 Aug 12:00 -> 18 Aug 16:00 is 14 days and 4 hours.
    expect(text).toMatch(/14\s*Days/);
    expect(text).toMatch(/4\s*Hours/);
    expect(text).toContain('Pre-order Entrusted');
    expect(text).toContain('See the launch details');
    expect(wrapper.find('a[href="/pre-order"]').exists()).toBe(true);
    expect(wrapper.find('a[href="/entrusted"]').exists()).toBe(true);
  });

  it('switches to a published state once the launch date has passed', async () => {
    const wrapper = await mountSection(launchBlock('2026-07-01T16:00:00+01:00'));
    const text = wrapper.text();

    expect(text).toContain('Published');
    expect(text).toContain('Launched on Tuesday, 18 August 2026');
    expect(text).not.toContain('Counting down');
    expect(text).toContain('Read about the launch');
    expect(text).not.toContain('Pre-order Entrusted');
    expect(wrapper.find('a[href="/rsvp"]').exists()).toBe(false);
  });
});
