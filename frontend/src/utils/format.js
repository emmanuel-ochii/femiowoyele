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

/**
 * Renders an amount held in minor units (kobo) as a display string.
 * Amounts are integers end to end to avoid floating-point drift on totals.
 */
export function formatMinorUnits(minorUnits, currency = 'NGN') {
  const symbols = { NGN: '\u20a6', USD: '$', GBP: '\u00a3', EUR: '\u20ac' };
  const symbol = symbols[currency] || '';

  return symbol + (Number(minorUnits || 0) / 100).toLocaleString('en-NG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}
