export const adminResources = [
  {
    slug: 'rsvps',
    label: 'Launch RSVPs',
    fields: [
      ['name', 'text'],
      ['email', 'text'],
      ['attending', 'checkbox'],
      ['guests', 'number'],
      ['note', 'textarea'],
    ],
  },
  {
    slug: 'articles',
    label: 'Articles',
    fields: [
      ['category_id', 'number'],
      ['pillar_id', 'number'],
      ['slug', 'text'],
      ['title', 'text'],
      ['excerpt', 'textarea'],
      ['body', 'textarea'],
      ['published_at', 'date'],
      ['is_featured', 'checkbox'],
    ],
  },
  {
    slug: 'journal-entries',
    label: 'Journal',
    fields: [
      ['category_id', 'number'],
      ['slug', 'text'],
      ['title', 'text'],
      ['excerpt', 'textarea'],
      ['body', 'textarea'],
      ['published_at', 'date'],
    ],
  },
  {
    slug: 'books',
    label: 'Books',
    fields: [
      ['slug', 'text'],
      ['title', 'text'],
      ['subtitle', 'text'],
      ['description', 'textarea'],
      ['cover_image_path', 'text'],
      ['is_featured', 'checkbox'],
      ['order', 'number'],
    ],
  },
  {
    slug: 'pillars',
    label: 'Pillars',
    fields: [
      ['slug', 'text'],
      ['title', 'text'],
      ['subtitle', 'text'],
      ['description', 'textarea'],
      ['order', 'number'],
    ],
  },
  {
    slug: 'projects',
    label: 'Projects',
    fields: [
      ['pillar_id', 'number'],
      ['slug', 'text'],
      ['title', 'text'],
      ['summary', 'textarea'],
      ['content', 'textarea'],
    ],
  },
  {
    slug: 'categories',
    label: 'Categories',
    fields: [
      ['slug', 'text'],
      ['name', 'text'],
      ['description', 'textarea'],
    ],
  },
  {
    slug: 'media-items',
    label: 'Media',
    fields: [
      ['pillar_id', 'number'],
      ['type', 'text'],
      ['slug', 'text'],
      ['title', 'text'],
      ['description', 'textarea'],
      ['url', 'text'],
      ['thumbnail_path', 'text'],
      ['published_at', 'date'],
    ],
  },
  {
    slug: 'impact-metrics',
    label: 'Impact Metrics',
    fields: [
      ['slug', 'text'],
      ['label', 'text'],
      ['value', 'text'],
      ['description', 'textarea'],
      ['order', 'number'],
    ],
  },
  {
    slug: 'quotes',
    label: 'Quotes',
    fields: [
      ['text', 'textarea'],
      ['source', 'text'],
      ['is_active', 'checkbox'],
    ],
  },
  {
    slug: 'convictions',
    label: 'Convictions',
    fields: [
      ['title', 'text'],
      ['description', 'textarea'],
      ['order', 'number'],
    ],
  },
  {
    slug: 'content-blocks',
    label: 'Content Blocks',
    fields: [
      ['slug', 'text'],
      ['title', 'text'],
      ['body', 'textarea'],
      ['context', 'text'],
      ['meta', 'json'],
      ['order', 'number'],
    ],
  },
];

export function resourceFor(slug) {
  return adminResources.find((resource) => resource.slug === slug) || adminResources[0];
}
