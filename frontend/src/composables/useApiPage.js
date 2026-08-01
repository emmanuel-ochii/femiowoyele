import { onMounted, ref, unref } from 'vue';
import { useContentStore } from '../stores/content';

export function useApiPage(path, params = {}) {
  const payload = ref(null);
  const loading = ref(true);
  const error = ref(null);
  const store = useContentStore();

  const load = async () => {
    const resolvedPath = typeof path === 'function' ? path() : unref(path);
    const resolvedParams = typeof params === 'function' ? params() : unref(params);

    loading.value = true;
    error.value = null;
    try {
      payload.value = await store.fetch(resolvedPath, resolvedParams);
    } catch (caught) {
      error.value = caught;
    } finally {
      loading.value = false;
    }
  };

  onMounted(load);

  return { payload, loading, error, load };
}
