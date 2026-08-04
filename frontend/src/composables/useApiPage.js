import { onMounted, ref, unref } from 'vue';
import { useContentStore } from '../stores/content';

/**
 * Loads a public API payload for a page.
 *
 * `path` and `params` may be values, refs, or getters so route-driven pages can
 * re-run `load()` on navigation. `retry` refetches past the store cache.
 */
export function useApiPage(path, params = {}) {
  const payload = ref(null);
  const loading = ref(true);
  const error = ref(null);
  const store = useContentStore();

  const load = async ({ force = false } = {}) => {
    const resolvedPath = typeof path === 'function' ? path() : unref(path);
    const resolvedParams = typeof params === 'function' ? params() : unref(params);

    loading.value = true;
    error.value = null;

    try {
      payload.value = await store.fetch(resolvedPath, resolvedParams, { force });
    } catch (caught) {
      error.value = caught;
    } finally {
      loading.value = false;
    }
  };

  const retry = () => load({ force: true });

  onMounted(load);

  return { payload, loading, error, load, retry };
}
