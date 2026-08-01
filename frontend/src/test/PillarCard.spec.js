import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import { createRouter, createWebHistory } from 'vue-router';
import PillarCard from '../components/cards/PillarCard.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: { template: '<div />' } },
    { path: '/work/:pillarSlug', component: { template: '<div />' } },
  ],
});

describe('PillarCard', () => {
  it('links to the pillar detail route and renders the editorial content', async () => {
    await router.push('/');

    const wrapper = mount(PillarCard, {
      global: { plugins: [router] },
      props: {
        pillar: {
          slug: 'enterprise',
          title: 'Enterprise',
          subtitle: 'Building durable organisations',
          description: 'Work across enterprise creation and operational clarity.',
          order: 1,
        },
      },
    });

    expect(wrapper.text()).toContain('Enterprise');
    expect(wrapper.attributes('href')).toBe('/work/enterprise');
  });
});
