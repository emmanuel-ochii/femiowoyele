<template>
  <div>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="eyebrow">CMS Resource</p>
        <h1 class="mt-3 font-serif text-4xl text-navy">{{ resource.label }}</h1>
      </div>
      <BaseButton variant="secondary" @click="resetForm">New item</BaseButton>
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_420px]">
      <div class="overflow-hidden border border-navy/10 bg-white">
        <div class="border-b border-navy/10 px-5 py-4 text-sm font-semibold text-navy">Existing content</div>
        <div v-if="loading" class="p-5 text-sm text-ink/60">Loading...</div>
        <div v-else class="divide-y divide-navy/10">
          <article v-for="item in items" :key="item.id" class="grid gap-3 p-5 md:grid-cols-[1fr_auto] md:items-center">
            <div>
              <p class="font-semibold text-navy">{{ item.title || item.label || item.name || item.slug || item.text }}</p>
              <p class="mt-1 line-clamp-2 text-sm leading-6 text-ink/60">{{ item.excerpt || item.description || item.body }}</p>
            </div>
            <div class="flex gap-2">
              <button class="focus-ring border border-navy/15 px-3 py-2 text-sm font-semibold text-navy" type="button" @click="edit(item)">
                Edit
              </button>
              <button class="focus-ring border border-red-200 px-3 py-2 text-sm font-semibold text-red-700" type="button" @click="remove(item)">
                Delete
              </button>
            </div>
          </article>
        </div>
      </div>

      <form class="border border-navy/10 bg-white p-5" @submit.prevent="save">
        <h2 class="font-serif text-2xl text-navy">{{ form.id ? 'Edit item' : 'Create item' }}</h2>
        <div class="mt-5 grid gap-4">
          <label v-for="[name, type] in resource.fields" :key="name" class="grid gap-2 text-sm font-semibold text-navy">
            {{ labelFor(name) }}
            <textarea
              v-if="type === 'textarea' || type === 'json'"
              v-model="form[name]"
              class="focus-ring min-h-28 border border-navy/15 px-3 py-2 font-normal"
            />
            <input
              v-else-if="type === 'checkbox'"
              v-model="form[name]"
              class="focus-ring h-5 w-5"
              type="checkbox"
            />
            <input
              v-else
              v-model="form[name]"
              class="focus-ring border border-navy/15 px-3 py-2 font-normal"
              :type="type"
            />
          </label>
        </div>
        <p v-if="message" class="mt-4 text-sm text-forest">{{ message }}</p>
        <p v-if="error" class="mt-4 text-sm text-red-700">{{ error }}</p>
        <div class="mt-6 flex gap-3">
          <BaseButton type="submit">{{ form.id ? 'Update' : 'Create' }}</BaseButton>
          <BaseButton variant="ghost" type="button" @click="resetForm">Clear</BaseButton>
        </div>
      </form>
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
const message = ref('');
const error = ref('');
const form = reactive({});

const labelFor = (name) => name.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

const normalizeForForm = (item = {}) => {
  Object.keys(form).forEach((key) => delete form[key]);
  form.id = item.id || null;

  resource.value.fields.forEach(([name, type]) => {
    const value = item[name];
    form[name] = type === 'json' ? JSON.stringify(value || {}, null, 2) : value ?? (type === 'checkbox' ? false : '');
  });
};

const payloadFromForm = () => {
  return resource.value.fields.reduce((payload, [name, type]) => {
    const value = form[name];

    if (type === 'number') {
      payload[name] = value === '' || value === null ? null : Number(value);
    } else if (type === 'checkbox') {
      payload[name] = Boolean(value);
    } else if (type === 'json') {
      payload[name] = value ? JSON.parse(value) : {};
    } else {
      payload[name] = value || null;
    }

    return payload;
  }, {});
};

const load = async () => {
  loading.value = true;
  items.value = await adminApi.list(resource.value.slug);
  loading.value = false;
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
  message.value = '';
  error.value = '';

  try {
    const payload = payloadFromForm();
    if (form.id) {
      await adminApi.update(resource.value.slug, form.id, payload);
      resetForm();
      message.value = 'Updated.';
    } else {
      await adminApi.create(resource.value.slug, payload);
      resetForm();
      message.value = 'Created.';
    }
    await load();
  } catch (caught) {
    error.value = caught.response?.data?.message || caught.message || 'Unable to save this item.';
  }
};

const remove = async (item) => {
  if (!window.confirm('Delete this item?')) return;
  await adminApi.destroy(resource.value.slug, item.id);
  await load();
};

watch(
  () => route.params.resource,
  async () => {
    resetForm();
    await load();
  },
);

onMounted(async () => {
  resetForm();
  await load();
});
</script>
