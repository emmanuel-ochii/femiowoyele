import { unref, watchEffect } from 'vue';

const SITE_NAME = 'Femi Owoyele';
const DEFAULT_DESCRIPTION =
  'The work of Femi Owoyele across enterprise, leadership, governance, sustainability, mentorship, authorship, and public engagement.';

function setMeta(selector, attribute, value) {
  if (typeof document === 'undefined') return;

  let tag = document.head.querySelector(selector);

  if (!tag) {
    tag = document.createElement('meta');
    const [key, val] = selector.replace(/^meta\[|\]$/g, '').split('=');
    tag.setAttribute(key, val.replace(/"/g, ''));
    document.head.appendChild(tag);
  }

  tag.setAttribute(attribute, value);
}

/**
 * Keeps the document title and the description/social tags in step with the
 * page being viewed. Accepts plain values, refs, or getters.
 */
export function usePageMeta(source) {
  watchEffect(() => {
    const resolved = typeof source === 'function' ? source() : unref(source);
    const { title, description } = resolved || {};

    const fullTitle = title ? `${title} — ${SITE_NAME}` : `${SITE_NAME} — Enterprise, leadership, and stewardship`;
    const summary = description || DEFAULT_DESCRIPTION;

    if (typeof document !== 'undefined') {
      document.title = fullTitle;
    }

    setMeta('meta[name="description"]', 'content', summary);
    setMeta('meta[property="og:title"]', 'content', fullTitle);
    setMeta('meta[property="og:description"]', 'content', summary);
    setMeta('meta[property="og:type"]', 'content', 'website');
    setMeta('meta[name="twitter:card"]', 'content', 'summary_large_image');
  });
}

export default usePageMeta;
