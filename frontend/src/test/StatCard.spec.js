import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import StatCard from '../components/cards/StatCard.vue';

describe('StatCard', () => {
  it('renders metric value, label, and description', () => {
    const wrapper = mount(StatCard, {
      props: {
        metric: {
          value: '1,200+',
          label: 'Builders mentored',
          description: 'Emerging leaders reached through mentorship.',
        },
      },
    });

    expect(wrapper.text()).toContain('1,200+');
    expect(wrapper.text()).toContain('Builders mentored');
  });
});
