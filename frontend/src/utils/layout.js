/**
 * Column classes for a card grid, capped by how many cards there actually are.
 *
 * A three-column grid holding one item reads as a broken layout rather than a
 * deliberate one, so sparse collections get a narrower track instead.
 */
export function cardGridClass(count) {
  if (count <= 1) return 'max-w-md';
  if (count === 2) return 'sm:grid-cols-2 lg:max-w-4xl';
  return 'sm:grid-cols-2 lg:grid-cols-3';
}

export default cardGridClass;
