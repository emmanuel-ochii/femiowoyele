import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import RsvpForm from '../components/forms/RsvpForm.vue';
import { contentApi } from '../services/contentApi';

vi.mock('../services/contentApi', () => ({
  contentApi: { post: vi.fn(), get: vi.fn() },
}));

/**
 * vee-validate settles its schema validation on a macrotask, so flushing
 * microtasks alone leaves the submit mid-flight. Wait on a real timer first.
 */
const settle = async () => {
  await new Promise((resolve) => setTimeout(resolve, 50));
  await flushPromises();
};

const fill = async (wrapper, { name = 'Ada Builder', email = 'ada@example.com' } = {}) => {
  await wrapper.find('#rsvp-name').setValue(name);
  await wrapper.find('#rsvp-email').setValue(email);
};

const chooseAttending = async (wrapper, attending) => {
  const radios = wrapper.findAll('input[type="radio"]');
  await radios[attending ? 0 : 1].setValue();
};

describe('RsvpForm', () => {
  beforeEach(() => vi.clearAllMocks());

  it('warns that the event is adults only', () => {
    const wrapper = mount(RsvpForm);
    expect(wrapper.text()).toContain('adults-only event');
  });

  it('asks only for the core closed-event RSVP details', () => {
    const wrapper = mount(RsvpForm);

    expect(wrapper.find('#rsvp-name').exists()).toBe(true);
    expect(wrapper.find('#rsvp-email').exists()).toBe(true);
    expect(wrapper.find('#rsvp-guests').exists()).toBe(false);
    expect(wrapper.text()).toContain('Will you be attending?');
    expect(wrapper.text()).toContain('Yes, I will be attending');
    expect(wrapper.text()).toContain("I won't be able to make it");
  });

  it('will not submit without an attendance answer', async () => {
    const wrapper = mount(RsvpForm);
    await fill(wrapper);
    await wrapper.find('form').trigger('submit');
    await settle();

    expect(contentApi.post).not.toHaveBeenCalled();
    expect(wrapper.text()).toContain('Please let us know whether you can join us.');
  });

  it('submits an attending answer without a guest count', async () => {
    contentApi.post.mockResolvedValue({ data: {}, meta: { updated: false } });

    const wrapper = mount(RsvpForm);
    await fill(wrapper);
    await chooseAttending(wrapper, true);
    await wrapper.find('form').trigger('submit');
    await settle();

    expect(contentApi.post).toHaveBeenCalledWith('/rsvp', {
      name: 'Ada Builder',
      email: 'ada@example.com',
      attending: true,
      note: null,
    });
    expect(wrapper.text()).toContain('Thank you for your RSVP.');
    expect(wrapper.text()).toContain('I look forward to celebrating this special milestone with you');
  });

  it('sends a decline without a guest count', async () => {
    contentApi.post.mockResolvedValue({ data: {}, meta: { updated: false } });

    const wrapper = mount(RsvpForm);
    await fill(wrapper);
    await chooseAttending(wrapper, false);
    await wrapper.find('form').trigger('submit');
    await settle();

    expect(contentApi.post).toHaveBeenCalledWith(
      '/rsvp',
      expect.objectContaining({ attending: false }),
    );
    expect(contentApi.post.mock.calls[0][1]).not.toHaveProperty('guests');
    expect(wrapper.text()).toContain('Although I will miss celebrating with you in person');
  });

  it('tells a returning guest their answer was updated', async () => {
    contentApi.post.mockResolvedValue({ data: {}, meta: { updated: true } });

    const wrapper = mount(RsvpForm);
    await fill(wrapper);
    await chooseAttending(wrapper, true);
    await wrapper.find('form').trigger('submit');
    await settle();

    expect(wrapper.text()).toContain('Your RSVP is updated.');
  });

  it('surfaces a server error without losing the answer', async () => {
    contentApi.post.mockRejectedValue({ response: { data: { message: 'Too many attempts.' } } });

    const wrapper = mount(RsvpForm);
    await fill(wrapper);
    await chooseAttending(wrapper, true);
    await wrapper.find('form').trigger('submit');
    await settle();

    expect(wrapper.text()).toContain('Too many attempts.');
    expect(wrapper.find('form').exists()).toBe(true);
  });
});
