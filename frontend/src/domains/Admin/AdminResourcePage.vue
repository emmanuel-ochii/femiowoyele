<template>
  <div>
    <div class="mb-8 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
      <div>
        <p class="eyebrow">Content type</p>
        <h1 class="display-3 mt-5 text-navy-900">{{ resource.label }}</h1>
        <p class="body-copy mt-3 max-w-2xl">{{ resource.description }}</p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <label class="sr-only" for="admin-search">Search {{ resource.label }}</label>
        <input
          id="admin-search"
          v-model="searchTerm"
          class="field-input min-w-0 sm:w-72"
          type="search"
          :placeholder="`Search ${resource.label.toLowerCase()}`"
        />
        <BaseButton v-if="canCreate" variant="outline" icon="arrowRight" @click="resetForm">New item</BaseButton>
      </div>
    </div>

    <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_440px]">
      <div class="overflow-hidden border border-navy-900/10 bg-white">
        <div class="flex items-center justify-between gap-4 border-b border-navy-900/10 px-5 py-4">
          <div>
            <p class="text-micro font-semibold uppercase text-ink-faint">Existing content</p>
            <p v-if="pagination.total !== null" class="mt-1 text-xs text-ink-faint">
              Showing {{ filteredItems.length }} of {{ pagination.total }} records
            </p>
          </div>
          <button
            class="text-micro font-semibold uppercase text-forest-700 transition-colors hover:text-forest-900"
            type="button"
            @click="load"
          >
            Refresh
          </button>
        </div>

        <div v-if="loading" class="space-y-3 p-5">
          <div v-for="n in 5" :key="n" class="skeleton h-20 w-full"></div>
        </div>

        <div v-else-if="!filteredItems.length" class="p-8">
          <p class="font-serif text-2xl text-navy-900">No matching records.</p>
          <p class="body-copy mt-3">{{ emptyStateCopy }}</p>
        </div>

        <div v-else class="divide-y divide-navy-900/10">
          <article
            v-for="item in filteredItems"
            :key="item.id"
            :class="[
              'grid gap-4 p-5 transition-colors md:grid-cols-[1fr_auto] md:items-center',
              form.id === item.id ? 'bg-sand-50' : 'bg-white hover:bg-sand-50/70',
            ]"
          >
            <button class="min-w-0 text-left" type="button" @click="edit(item)">
              <div class="flex flex-wrap items-center gap-2">
                <p class="font-medium text-navy-900">{{ primaryLabel(item) }}</p>
                <span
                  v-for="badge in badgesFor(item)"
                  :key="badge"
                  class="border border-navy-900/10 px-2 py-1 text-[0.68rem] font-semibold uppercase text-ink-faint"
                >
                  {{ badge }}
                </span>
              </div>
              <p v-if="metaLine(item)" class="mt-1 text-xs font-medium text-forest-700">{{ metaLine(item) }}</p>
              <p class="mt-2 line-clamp-2 text-sm leading-6 text-ink-muted">{{ summaryFor(item) }}</p>
            </button>

            <div class="flex gap-2">
              <button
                class="border border-navy-900/15 px-3 py-2 text-[0.8rem] font-semibold text-navy-900 transition-colors hover:border-forest-800 hover:text-forest-800"
                type="button"
                @click="edit(item)"
              >
                {{ isReadOnly ? 'View' : 'Edit' }}
              </button>
              <button
                v-if="canDelete"
                class="border border-red-200 px-3 py-2 text-[0.8rem] font-semibold text-red-700 transition-colors hover:border-red-500 hover:bg-red-50"
                type="button"
                @click="remove(item)"
              >
                Delete
              </button>
            </div>
          </article>
        </div>
      </div>

      <aside class="h-fit border border-navy-900/10 bg-white p-6 xl:sticky xl:top-28">
        <div class="border-b border-navy-900/10 pb-5">
          <p class="text-micro font-semibold uppercase text-forest-700">{{ formMode }}</p>
          <h2 class="mt-2 font-serif text-2xl text-navy-900">{{ formTitle }}</h2>
          <p v-if="isReadOnly" class="mt-3 text-sm leading-6 text-ink-muted">
            This section is intentionally view-only in the admin UI.
          </p>
        </div>

        <form class="mt-7" @submit.prevent="save">
          <div v-if="isReadOnly && !form.id" class="border-y border-navy-900/10 py-8">
            <p class="text-sm leading-6 text-ink-muted">
              Choose a record from the list to review the submitted details.
            </p>
          </div>

          <div v-else class="grid gap-6">
            <label v-for="field in resource.fields" :key="field.name" class="grid gap-2">
              <span class="field-label">{{ field.label || labelFor(field.name) }}</span>

              <select
                v-if="field.type === 'select'"
                v-model="form[field.name]"
                class="field-input"
                :disabled="fieldReadonly(field)"
                :aria-describedby="fieldHelpId(field)"
              >
                <option value="">Select {{ (field.label || labelFor(field.name)).toLowerCase() }}</option>
                <option v-for="option in optionsFor(field)" :key="String(option.value)" :value="option.value">
                  {{ option.label }}
                </option>
              </select>

              <textarea
                v-else-if="field.type === 'textarea' || field.type === 'json'"
                v-model="form[field.name]"
                class="field-input min-h-28 resize-y"
                :class="fieldErrors[field.name] && 'field-input-invalid'"
                :rows="field.rows || (field.type === 'json' ? 10 : 5)"
                :disabled="fieldReadonly(field)"
                :placeholder="field.placeholder"
                :aria-describedby="fieldHelpId(field)"
              />

              <span v-else-if="field.type === 'checkbox'" class="flex items-center gap-3">
                <input
                  v-model="form[field.name]"
                  class="h-5 w-5 accent-forest-800"
                  type="checkbox"
                  :disabled="fieldReadonly(field)"
                />
                <span class="text-sm text-ink-muted">{{ form[field.name] ? 'Yes' : 'No' }}</span>
              </span>

              <input
                v-else
                v-model="form[field.name]"
                class="field-input"
                :class="fieldErrors[field.name] && 'field-input-invalid'"
                :type="field.type || 'text'"
                :disabled="fieldReadonly(field)"
                :placeholder="field.placeholder"
                :aria-describedby="fieldHelpId(field)"
              />

              <p v-if="field.help" :id="`${field.name}-help`" class="text-xs leading-5 text-ink-faint">{{ field.help }}</p>
              <p v-if="fieldErrors[field.name]" class="field-error">{{ fieldErrors[field.name][0] }}</p>
            </label>
          </div>

          <p v-if="message" class="mt-5 text-sm font-medium text-forest-700" role="status">{{ message }}</p>
          <p v-if="error" class="mt-5 text-sm font-medium text-red-700" role="alert">{{ error }}</p>

          <div v-if="!isReadOnly" class="mt-8 flex flex-wrap gap-x-6 gap-y-3">
            <BaseButton type="submit" :loading="saving">{{ form.id ? 'Update' : 'Create' }}</BaseButton>
            <BaseButton variant="ghost" type="button" @click="resetForm">Clear</BaseButton>
          </div>
        </form>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import BaseButton from '../../components/ui/BaseButton.vue';
import { adminApi } from '../../services/adminApi';
import { resourceFor } from './adminResources';

const route = useRoute();
const resource = computed(() => resourceFor(route.params.resource));
const items = ref([]);
const loading = ref(false);
const saving = ref(false);
const message = ref('');
const error = ref('');
const searchTerm = ref('');
const fieldErrors = ref({});
const referenceOptions = ref({});
const pagination = ref({ total: null });
const form = reactive({});

const canCreate = computed(() => resource.value.allowCreate !== false && !resource.value.readOnly);
const canDelete = computed(() => resource.value.allowDelete !== false && !resource.value.readOnly);
const isReadOnly = computed(() => resource.value.readOnly === true);
const singularLabel = computed(() => resource.value.singularLabel || resource.value.label.toLowerCase());
const formMode = computed(() => (isReadOnly.value ? 'Record details' : form.id ? 'Editing' : 'Creating'));
const formTitle = computed(() => {
  if (isReadOnly.value) return form.id ? primaryLabel(form) : `Select a ${singularLabel.value} record`;
  return form.id ? primaryLabel(form) : `New ${singularLabel.value}`;
});
const emptyStateCopy = computed(() => {
  if (searchTerm.value.trim()) return 'Clear the search to see every record in this section.';
  if (isReadOnly.value) return 'New records will appear here after visitors submit the public form.';
  return 'Create a new item if this section should have content.';
});

const labelFor = (name) => name.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
const fieldReadonly = (field) => isReadOnly.value || field.readonly === true;
const fieldHelpId = (field) => (field.help ? `${field.name}-help` : undefined);

const filteredItems = computed(() => {
  const query = searchTerm.value.trim().toLowerCase();
  if (!query) return items.value;

  return items.value.filter((item) =>
    [primaryLabel(item), summaryFor(item), metaLine(item), item.email, item.slug, item.subject, item.message, item.source, item.created_at]
      .filter(Boolean)
      .join(' ')
      .toLowerCase()
      .includes(query),
  );
});

const defaultValueFor = (field) => {
  if (field.type === 'checkbox') return false;
  if (field.type === 'json') return '{}';
  return '';
};

const normalizeForForm = (item = {}) => {
  Object.keys(form).forEach((key) => delete form[key]);
  fieldErrors.value = {};
  form.id = item.id || null;

  resource.value.fields.forEach((field) => {
    const value = item[field.name];
    form[field.name] = field.type === 'json' ? JSON.stringify(value || {}, null, 2) : value ?? defaultValueFor(field);
  });
};

const payloadFromForm = () => {
  fieldErrors.value = {};

  return resource.value.fields.reduce((payload, field) => {
    const value = form[field.name];

    if (field.type === 'number' || field.valueType === 'number') {
      payload[field.name] = value === '' || value === null ? null : Number(value);
    } else if (field.type === 'checkbox') {
      payload[field.name] = Boolean(value);
    } else if (field.type === 'json') {
      try {
        payload[field.name] = value ? JSON.parse(value) : {};
      } catch {
        fieldErrors.value = { ...fieldErrors.value, [field.name]: ['Enter valid JSON before saving.'] };
        throw new Error('Fix the highlighted field before saving.');
      }
    } else {
      payload[field.name] = value === '' ? null : value;
    }

    return payload;
  }, {});
};

const optionsFor = (field) => {
  if (Array.isArray(field.options)) {
    return field.options.map((option) => (typeof option === 'string' ? { value: option, label: labelFor(option) } : option));
  }

  if (!field.source) return [];

  return (referenceOptions.value[field.source] || []).map((item) => ({
    value: item.id,
    label: item[field.optionLabel || 'title'] || item.name || item.slug || `#${item.id}`,
  }));
};

const primaryLabel = (item = {}) =>
  item.title ||
  item.label ||
  item.name ||
  item.email ||
  item.media_item?.title ||
  item.gallery?.title ||
  item.slug ||
  item.text ||
  `#${item.id || ''}`;
const summaryFor = (item = {}) => {
  if (resource.value.slug === 'rsvps') return item.note || item.email || 'No note provided.';
  if (resource.value.slug === 'gallery-items') {
    return item.media_item?.description || item.gallery?.description || 'Gallery placement record.';
  }
  return item.excerpt || item.description || item.body || item.message || item.note || item.subtitle || item.source || 'No summary provided.';
};
const metaLine = (item = {}) => {
  if (resource.value.slug === 'galleries' && Array.isArray(item.media_items)) {
    return `${item.media_items.length} media ${item.media_items.length === 1 ? 'item' : 'items'}`;
  }

  if (resource.value.slug === 'gallery-items') {
    return [item.gallery?.title, item.media_item?.type && labelFor(item.media_item.type), item.order !== undefined && `Order ${item.order}`]
      .filter(Boolean)
      .join(' / ');
  }

  const bits = [
    item.category?.name,
    item.pillar?.title,
    item.gallery?.title,
    item.media_item?.type && labelFor(item.media_item.type),
    item.type && labelFor(item.type),
    item.published_at,
    item.context,
    item.created_at,
  ].filter(Boolean);

  return bits.join(' / ');
};

const badgesFor = (item = {}) => {
  const badges = [];
  if (item.is_featured) badges.push('Featured');
  if (item.is_active === true) badges.push('Active');
  if (item.is_active === false && resource.value.slug === 'quotes') badges.push('Inactive');
  if (resource.value.slug === 'rsvps') {
    badges.push(item.attending ? `Attending +${Number(item.guests || 0)}` : 'Declined');
  }
  return badges;
};

const loadReferenceOptions = async () => {
  const sources = [...new Set(resource.value.fields.map((field) => field.source).filter(Boolean))];

  await Promise.all(
    sources.map(async (source) => {
      const result = await adminApi.list(source, { per_page: 100 });
      referenceOptions.value = {
        ...referenceOptions.value,
        [source]: result.data || [],
      };
    }),
  );
};

const load = async () => {
  loading.value = true;
  error.value = '';

  try {
    const result = await adminApi.list(resource.value.slug, { per_page: resource.value.perPage || 50 });
    items.value = result.data || [];
    pagination.value = { total: result.meta?.total ?? items.value.length };
  } catch (caught) {
    error.value = caught.response?.data?.message || caught.message || 'Unable to load this section.';
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  message.value = '';
  error.value = '';
  normalizeForForm();
};

const edit = (item) => {
  message.value = '';
  error.value = '';
  normalizeForForm(item);
};

const save = async () => {
  if (isReadOnly.value) return;

  message.value = '';
  error.value = '';
  saving.value = true;

  try {
    const payload = payloadFromForm();
    const wasUpdate = Boolean(form.id);

    if (wasUpdate) {
      await adminApi.update(resource.value.slug, form.id, payload);
    } else {
      await adminApi.create(resource.value.slug, payload);
    }
    await load();
    resetForm();
    message.value = wasUpdate ? 'Updated.' : 'Created.';
  } catch (caught) {
    fieldErrors.value = caught.response?.data?.errors || fieldErrors.value;
    error.value = caught.response?.data?.message || caught.message || 'Unable to save this item.';
  } finally {
    saving.value = false;
  }
};

const remove = async (item) => {
  if (!window.confirm(`Delete "${primaryLabel(item)}"?`)) return;

  try {
    await adminApi.destroy(resource.value.slug, item.id);
    await load();
    resetForm();
    message.value = 'Deleted.';
  } catch (caught) {
    error.value = caught.response?.data?.message || caught.message || 'Unable to delete this item.';
  }
};

const bootstrap = async () => {
  searchTerm.value = '';
  resetForm();
  await loadReferenceOptions();
  await load();
};

watch(
  () => route.params.resource,
  async () => {
    await bootstrap();
  },
);

onMounted(bootstrap);
</script>
