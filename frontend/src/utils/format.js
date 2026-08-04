/**
 * Renders API dates as "12 March 2026". Falls back to the raw value so a
 * pre-formatted or unexpected string is never swallowed.
 */
export function formatDate(value) {
  if (!value) return '';

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return String(value);

  return new Intl.DateTimeFormat('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
}

/** Rough reading time for long-form bodies, used as an editorial cue. */
export function readingTime(body) {
  const words = String(body || '').trim().split(/\s+/).filter(Boolean).length;
  if (!words) return '';

  return `${Math.max(1, Math.round(words / 220))} min read`;
}
